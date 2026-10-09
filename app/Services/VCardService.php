<?php

namespace App\Services;

use App\Models\User;
use App\Models\VCard;
use App\Models\VCardTemplate;
use App\VCardStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class VCardService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(User $user, array $data): VCard
    {
        return DB::transaction(function () use ($user, $data): VCard {
            $templateId = VCardTemplate::query()
                ->where('is_active', true)
                ->value('id');

            $vCard = VCard::query()->create([
                ...$this->attributes($data),
                ...$this->storeImages($user, $data),
                'user_id' => $user->id,
                'v_card_template_id' => $templateId,
                'slug' => $this->uniqueSlug((string) $data['public_name']),
                'accent_color' => '#000000',
                'status' => VCardStatus::Draft,
            ]);

            $this->syncLinks($vCard, $data['links'] ?? []);

            return $vCard->load(['links', 'qrCode']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(VCard $vCard, array $data): VCard
    {
        return DB::transaction(function () use ($vCard, $data): VCard {
            $attributes = [
                ...$this->attributes($data),
                ...$this->replaceImages($vCard, $data),
                'accent_color' => '#000000',
            ];

            $vCard->update($attributes);
            $this->syncLinks($vCard, $data['links'] ?? []);

            return $vCard->fresh(['links', 'qrCode']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function attributes(array $data): array
    {
        return Arr::only($data, [
            'title',
            'public_name',
            'organization',
            'job_title',
            'phone',
            'public_email',
            'website',
            'description',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string>
     */
    private function storeImages(User $user, array $data): array
    {
        $paths = [];

        foreach (['photo', 'cover', 'logo'] as $image) {
            if (($data[$image] ?? null) instanceof UploadedFile) {
                $paths[$image.'_path'] = $this->storeImage(
                    $data[$image],
                    "vcards/{$user->id}/{$image}",
                );
            }
        }

        return $paths;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string|null>
     */
    private function replaceImages(VCard $vCard, array $data): array
    {
        $paths = [];

        foreach (['photo', 'cover', 'logo'] as $image) {
            $pathAttribute = $image.'_path';
            $currentPath = $vCard->{$pathAttribute};

            if (($data['remove_'.$image] ?? false) && is_string($currentPath)) {
                Storage::disk('public')->delete($currentPath);
                $paths[$pathAttribute] = null;
            }

            if (($data[$image] ?? null) instanceof UploadedFile) {
                $newPath = $this->storeImage(
                    $data[$image],
                    "vcards/{$vCard->user_id}/{$image}",
                );

                if (is_string($currentPath)) {
                    Storage::disk('public')->delete($currentPath);
                }

                $paths[$pathAttribute] = $newPath;
            }
        }

        return $paths;
    }

    private function storeImage(UploadedFile $image, string $directory): string
    {
        $path = $image->storePublicly($directory, 'public');

        if ($path === false) {
            throw new RuntimeException('No fue posible guardar la imagen de la vCard.');
        }

        return $path;
    }

    /**
     * @param  array<int, array<string, mixed>>  $links
     */
    private function syncLinks(VCard $vCard, array $links): void
    {
        $labels = [
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'linkedin' => 'LinkedIn',
            'whatsapp' => 'WhatsApp',
            'tiktok' => 'TikTok',
            'youtube' => 'YouTube',
            'x' => 'X',
            'github' => 'GitHub',
        ];

        $submittedTypes = [];

        foreach ($links as $position => $link) {
            $url = trim((string) ($link['url'] ?? ''));

            if ($url === '') {
                continue;
            }

            $type = (string) $link['type'];
            $submittedTypes[] = $type;

            $vCard->links()->updateOrCreate(
                ['type' => $type],
                [
                    'label' => trim((string) ($link['label'] ?? '')) ?: $labels[$type],
                    'url' => $url,
                    'sort_order' => $position,
                    'is_active' => (bool) ($link['is_active'] ?? true),
                ],
            );
        }

        $linksToDelete = $vCard->links();

        if ($submittedTypes !== []) {
            $linksToDelete->whereNotIn('type', $submittedTypes);
        }

        $linksToDelete->delete();
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'vcard';

        do {
            $slug = Str::limit($base, 80, '').'-'.Str::lower(Str::random(6));
        } while (VCard::query()->where('slug', $slug)->exists());

        return $slug;
    }
}
