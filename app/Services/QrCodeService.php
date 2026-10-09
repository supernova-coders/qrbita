<?php

namespace App\Services;

use App\Models\QrCode as QrCodeModel;
use App\Models\VCard;
use App\QrCodeStatus;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Str;

class QrCodeService
{
    public function ensureFor(VCard $vCard): QrCodeModel
    {
        return $vCard->qrCode()->firstOrCreate([], [
            'code' => Str::lower(Str::random(24)),
            'destination_url' => route('public.vcards.show', $vCard, absolute: false),
            'image_url' => route('vcards.qr', $vCard, absolute: false),
            'status' => QrCodeStatus::Active,
        ]);
    }

    public function png(QrCodeModel $qrCode): string
    {
        $trackingUrl = route('public.qr.redirect', [
            'qrCode' => $qrCode->code,
        ]);

        $image = new QrCode(
            data: $trackingUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 720,
            margin: 28,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        return (new PngWriter)->write($image)->getString();
    }
}
