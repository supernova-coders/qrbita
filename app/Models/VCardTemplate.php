<?php

namespace App\Models;

use Database\Factories\VCardTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property array<string, mixed>|null $structure
 * @property bool $is_active
 * @property-read Collection<int, VCard> $vCards
 */
#[Fillable(['name', 'description', 'structure', 'is_active'])]
class VCardTemplate extends Model
{
    /** @use HasFactory<VCardTemplateFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'structure' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /** @return HasMany<VCard, $this> */
    public function vCards(): HasMany
    {
        return $this->hasMany(VCard::class);
    }
}
