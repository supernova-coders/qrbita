<?php

namespace App\Http\Controllers;

use App\InteractionType;
use App\Models\VCard;
use App\Services\InteractionRecorder;
use App\Services\VCardFileService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class VCardContactController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        Request $request,
        VCard $vCard,
        VCardFileService $fileService,
        InteractionRecorder $recorder,
    ): Response {
        abort_unless($vCard->isPublished(), 404);

        $recorder->record($request, $vCard, InteractionType::ContactSave);
        $filename = Str::slug($vCard->public_name) ?: 'contacto';

        return response($fileService->make($vCard), 200, [
            'Content-Type' => 'text/vcard; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.vcf\"",
            'Cache-Control' => 'no-store, private',
        ]);
    }
}
