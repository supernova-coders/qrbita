<?php

namespace App\Http\Controllers;

use App\InteractionType;
use App\Models\VCard;
use App\Models\VCardLink;
use App\Services\InteractionRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VCardLinkRedirectController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        Request $request,
        VCard $vCard,
        VCardLink $link,
        InteractionRecorder $recorder,
    ): RedirectResponse {
        abort_unless(
            $vCard->isPublished()
                && $link->v_card_id === $vCard->id
                && $link->is_active,
            404,
        );

        abort_unless(in_array(parse_url($link->url, PHP_URL_SCHEME), ['http', 'https'], true), 404);

        $recorder->record(
            $request,
            $vCard,
            InteractionType::LinkClick,
            link: $link,
        );

        return redirect()->away($link->url);
    }
}
