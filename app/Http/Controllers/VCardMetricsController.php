<?php

namespace App\Http\Controllers;

use App\Http\Resources\VCardResource;
use App\InteractionType;
use App\Models\Interaction;
use App\Models\VCard;
use App\Models\VCardLink;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class VCardMetricsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, VCard $vCard): Response
    {
        Gate::authorize('view', $vCard);

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $from = Carbon::parse($validated['from'] ?? now()->subDays(29))->startOfDay();
        $to = Carbon::parse($validated['to'] ?? now())->endOfDay();
        $baseQuery = $vCard->interactions()->whereBetween('occurred_at', [$from, $to]);
        $counts = (clone $baseQuery)
            ->selectRaw('event_type, count(*) as aggregate')
            ->groupBy('event_type')
            ->pluck('aggregate', 'event_type');
        $devices = (clone $baseQuery)
            ->selectRaw('device, count(*) as aggregate')
            ->groupBy('device')
            ->orderByDesc('aggregate')
            ->get()
            ->map(fn (Interaction $row): array => [
                'label' => (string) ($row->getAttribute('device') ?: 'other'),
                'count' => (int) $row->getAttribute('aggregate'),
            ]);
        $daily = (clone $baseQuery)
            ->selectRaw('date(occurred_at) as date, count(*) as aggregate')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn (Interaction $row): array => [
                'date' => (string) $row->getAttribute('date'),
                'count' => (int) $row->getAttribute('aggregate'),
            ]);
        $topLinks = $vCard->links()
            ->withCount(['interactions as interactions_count' => function (Builder $query) use ($from, $to): void {
                $query->where('event_type', InteractionType::LinkClick->value)
                    ->whereBetween('occurred_at', [$from, $to]);
            }])
            ->orderByDesc('interactions_count')
            ->limit(5)
            ->get()
            ->map(fn (VCardLink $link) => [
                'label' => $link->label,
                'type' => $link->type,
                'count' => $link->interactions_count,
            ]);

        $vCard->load(['links', 'qrCode']);

        return Inertia::render('vcards/metrics', [
            'vCard' => VCardResource::make($vCard)->resolve($request),
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'metrics' => [
                'views' => (int) ($counts[InteractionType::View->value] ?? 0),
                'scans' => (int) ($counts[InteractionType::Scan->value] ?? 0),
                'link_clicks' => (int) ($counts[InteractionType::LinkClick->value] ?? 0),
                'contact_saves' => (int) ($counts[InteractionType::ContactSave->value] ?? 0),
                'devices' => $devices,
                'daily' => $daily,
                'top_links' => $topLinks,
            ],
        ]);
    }
}
