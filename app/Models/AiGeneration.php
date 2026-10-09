<?php

namespace App\Models;

use App\AiGenerationStatus;
use Database\Factories\AiGenerationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $v_card_id
 * @property string $prompt
 * @property array<string, mixed>|null $result
 * @property string $model
 * @property AiGenerationStatus $status
 * @property string|null $error_message
 * @property-read User $user
 * @property-read VCard|null $vCard
 */
#[Fillable(['user_id', 'v_card_id', 'prompt', 'result', 'model', 'status', 'error_message'])]
class AiGeneration extends Model
{
    /** @use HasFactory<AiGenerationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'result' => 'array',
            'status' => AiGenerationStatus::class,
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<VCard, $this> */
    public function vCard(): BelongsTo
    {
        return $this->belongsTo(VCard::class);
    }
}
