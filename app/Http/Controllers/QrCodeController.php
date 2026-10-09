<?php

namespace App\Http\Controllers;

use App\Models\VCard;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class QrCodeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        Request $request,
        VCard $vCard,
        QrCodeService $service,
    ): Response {
        Gate::authorize('view', $vCard);
        $qrCode = $vCard->qrCode ?? $service->ensureFor($vCard);
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return response($service->png($qrCode), 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => $disposition.'; filename="qrbita-'.$vCard->slug.'.png"',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
