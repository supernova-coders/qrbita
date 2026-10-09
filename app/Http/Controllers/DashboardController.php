<?php

namespace App\Http\Controllers;

use App\Http\Resources\VCardResource;
use App\InteractionType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $vCard = $request->user()->vCard()->with(['links', 'qrCode'])->first();
        $metrics = [
            'views' => 0,
            'scans' => 0,
            'link_clicks' => 0,
            'contact_saves' => 0,
        ];

        if ($vCard) {
            $counts = $vCard->interactions()
                ->selectRaw('event_type, count(*) as aggregate')
                ->groupBy('event_type')
                ->pluck('aggregate', 'event_type');

            $metrics = [
                'views' => (int) ($counts[InteractionType::View->value] ?? 0),
                'scans' => (int) ($counts[InteractionType::Scan->value] ?? 0),
                'link_clicks' => (int) ($counts[InteractionType::LinkClick->value] ?? 0),
                'contact_saves' => (int) ($counts[InteractionType::ContactSave->value] ?? 0),
            ];
        }

        return Inertia::render('dashboard', [
            'vCard' => $vCard
                ? VCardResource::make($vCard)->resolve($request)
                : null,
            'metrics' => $metrics,
            'aiConfigured' => filled(config('services.openai.api_key')),
        ]);
    }
}
