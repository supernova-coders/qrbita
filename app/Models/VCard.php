<?php

namespace App\Models;

use App\VCardStatus;
use Database\Factories\VCardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $v_card_template_id
 * @property string $slug
 * @property string $title
 * @property string $public_name
 * @property string|null $organization
 * @property string|null $job_title
 * @property string|null $phone
 * @property string|null $public_email
 * @property string|null $website
 * @property string|null $description
 * @property string|null $photo_path
 * @property string|null $cover_path
 * @property string|null $logo_path
 * @property string $accent_color
 * @property VCardStatus $status
 * @property Carbon|null $published_at
 * @property-read User $user
 * @property-read VCardTemplate|null $template
 * @property-read Collection<int, VCardLink> $links
 * @property-read QrCode|null $qrCode
 * @property-read Collection<int, Interaction> $interactions
 * @property-read Collection<int, AiGeneration> $aiGenerations
 */
#[Fillable([
    'user_id',
    'v_card_template_id',
    'slug',
    'title',
    'public_name',
    'organization',
    'job_title',
    'phone',
    'public_email',
    'website',
    'description',
    'photo_path',
    'cover_path',
    'logo_path',
    'accent_color',
    'status',
    'published_at',
])]
class VCard extends Model
{
    /** @use HasFactory<VCardFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => VCardStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<VCardTemplate, $this> */
    public function template(): BelongsTo
    {
        return $this->belongsTo(VCardTemplate::class, 'v_card_template_id');
    }

    /** @return HasMany<VCardLink, $this> */
    public function links(): HasMany
    {
        return $this->hasMany(VCardLink::class)->orderBy('sort_order');
    }

    /** @return HasOne<QrCode, $this> */
    public function qrCode(): HasOne
    {
        return $this->hasOne(QrCode::class);
    }

    /** @return HasMany<Interaction, $this> */
    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    /** @return HasMany<AiGeneration, $this> */
    public function aiGenerations(): HasMany
    {
        return $this->hasMany(AiGeneration::class);
    }

    public function isPublished(): bool
    {
        return $this->status === VCardStatus::Published;
    }
}
