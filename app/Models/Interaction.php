<?php

namespace App\Models;

use App\InteractionType;
use Database\Factories\InteractionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $v_card_id
 * @property int|null $qr_code_id
 * @property int|null $v_card_link_id
 * @property InteractionType $event_type
 * @property string|null $country
 * @property string|null $city
 * @property string $device
 * @property string $browser
 * @property string|null $ip_hash
 * @property Carbon $occurred_at
 * @property-read VCard $vCard
 * @property-read QrCode|null $qrCode
 * @property-read VCardLink|null $vCardLink
 */
#[Fillable([
    'v_card_id',
    'qr_code_id',
    'v_card_link_id',
    'event_type',
    'country',
    'city',
    'device',
    'browser',
    'ip_hash',
    'occurred_at',
])]
class Interaction extends Model
{
    /** @use HasFactory<InteractionFactory> */
    use HasFactory;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'event_type' => InteractionType::class,
            'occurred_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<VCard, $this> */
    public function vCard(): BelongsTo
    {
        return $this->belongsTo(VCard::class);
    }

    /** @return BelongsTo<QrCode, $this> */
    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(QrCode::class);
    }

    /** @return BelongsTo<VCardLink, $this> */
    public function vCardLink(): BelongsTo
    {
        return $this->belongsTo(VCardLink::class);
    }
}
