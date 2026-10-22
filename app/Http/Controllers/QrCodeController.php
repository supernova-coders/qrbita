<?php

namespace App\Http\Controllers;

use App\Models\VCard;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QrCodeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        Request $request,
        VCard $vCard,
        QrCodeService $service,
    ): Response|StreamedResponse {
        Gate::authorize('view', $vCard);
        $qrCode = $vCard->qrCode ?? $service->ensureFor($vCard);
        $png = $service->png($qrCode);

        if ($request->boolean('download')) {
            return response()->streamDownload(
                static function () use ($png): void {
                    echo $png;
                },
                'qrbita-'.$vCard->slug.'.png',
                [
                    'Content-Type' => 'image/png',
                    'Cache-Control' => 'no-store, private',
                ],
            );
        }

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'inline; filename="qrbita-'.$vCard->slug.'.png"',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
