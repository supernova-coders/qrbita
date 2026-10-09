<?php

namespace App\Models;

use App\QrCodeStatus;
use Database\Factories\QrCodeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $v_card_id
 * @property string $code
 * @property string $destination_url
 * @property string $image_url
 * @property QrCodeStatus $status
 * @property-read VCard $vCard
 * @property-read Collection<int, Interaction> $interactions
 */
#[Fillable(['v_card_id', 'code', 'destination_url', 'image_url', 'status'])]
class QrCode extends Model
{
    /** @use HasFactory<QrCodeFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['status' => QrCodeStatus::class];
    }

    /** @return BelongsTo<VCard, $this> */
    public function vCard(): BelongsTo
    {
        return $this->belongsTo(VCard::class);
    }

    /** @return HasMany<Interaction, $this> */
    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }
}
