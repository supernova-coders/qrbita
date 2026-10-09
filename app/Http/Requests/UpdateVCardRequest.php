<?php

namespace App\Http\Requests;

use App\Models\VCard;

class UpdateVCardRequest extends StoreVCardRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $vCard = $this->route('vCard');

        return $vCard instanceof VCard
            && ($this->user()?->can('update', $vCard) ?? false);
    }
}
