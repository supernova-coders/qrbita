<?php

namespace App\Http\Controllers;

use App\Models\VCard;
use App\Services\QrCodeService;
use App\VCardStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PublishedVCardController extends Controller
{
    public function store(
        Request $request,
        VCard $vCard,
        QrCodeService $qrCodeService,
    ): RedirectResponse {
        Gate::authorize('update', $vCard);

        $vCard->update([
            'status' => VCardStatus::Published,
            'published_at' => $vCard->published_at ?? now(),
        ]);
        $qrCodeService->ensureFor($vCard);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Tu vCard ya está publicada y lista para compartir.',
        ]);

        return back();
    }

    public function destroy(Request $request, VCard $vCard): RedirectResponse
    {
        Gate::authorize('update', $vCard);

        $vCard->update(['status' => VCardStatus::Unpublished]);

        Inertia::flash('toast', [
            'type' => 'info',
            'message' => 'Tu vCard fue despublicada. El QR seguirá siendo el mismo al republicarla.',
        ]);

        return back();
    }
}
