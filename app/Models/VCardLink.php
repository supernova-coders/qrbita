<?php

namespace App\Models;

use Database\Factories\VCardLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $v_card_id
 * @property string $type
 * @property string $label
 * @property string $url
 * @property int $sort_order
 * @property bool $is_active
 * @property-read VCard $vCard
 * @property-read Collection<int, Interaction> $interactions
 */
#[Fillable(['v_card_id', 'type', 'label', 'url', 'sort_order', 'is_active'])]
class VCardLink extends Model
{
    /** @use HasFactory<VCardLinkFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
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
