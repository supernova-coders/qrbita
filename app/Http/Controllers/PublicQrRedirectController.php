<?php

namespace App\Http\Controllers;

use App\InteractionType;
use App\Models\QrCode;
use App\QrCodeStatus;
use App\Services\InteractionRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicQrRedirectController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        Request $request,
        QrCode $qrCode,
        InteractionRecorder $recorder,
    ): RedirectResponse {
        $qrCode->load('vCard');

        abort_unless(
            $qrCode->status === QrCodeStatus::Active && $qrCode->vCard->isPublished(),
            404,
        );

        $recorder->record(
            $request,
            $qrCode->vCard,
            InteractionType::Scan,
            $qrCode,
        );

        return redirect()->route('public.vcards.show', $qrCode->vCard);
    }
}
