<?php

namespace App\Http\Controllers;

use App\Http\Resources\PublicVCardResource;
use App\InteractionType;
use App\Models\VCard;
use App\Services\InteractionRecorder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicVCardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        Request $request,
        VCard $vCard,
        InteractionRecorder $recorder,
    ): Response {
        abort_unless($vCard->isPublished(), 404);

        $vCard->load([
            'links' => fn ($query) => $query->where('is_active', true),
        ]);
        $recorder->record($request, $vCard, InteractionType::View);

        return Inertia::render('public/vcard', [
            'vCard' => PublicVCardResource::make($vCard)->resolve($request),
        ]);
    }
}
