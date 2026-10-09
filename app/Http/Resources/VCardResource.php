<?php

namespace App\Http\Resources;

use App\Models\VCard;
use App\Models\VCardLink;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin VCard */
class VCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'public_name' => $this->public_name,
            'organization' => $this->organization,
            'job_title' => $this->job_title,
            'phone' => $this->phone,
            'public_email' => $this->public_email,
            'website' => $this->website,
            'description' => $this->description,
            'accent_color' => $this->accent_color,
            'status' => $this->status->value,
            'published_at' => $this->published_at?->toIso8601String(),
            'photo_url' => $this->mediaUrl($this->photo_path),
            'cover_url' => $this->mediaUrl($this->cover_path),
            'logo_url' => $this->mediaUrl($this->logo_path),
            'public_url' => route('public.vcards.show', $this->resource),
            'qr_image_url' => $this->qrCode
                ? route('vcards.qr', $this->resource)
                : null,
            'qr_download_url' => $this->qrCode
                ? route('vcards.qr', $this->resource).'?download=1'
                : null,
            'links' => $this->whenLoaded('links', fn () => $this->links
                ->map(fn (VCardLink $link) => [
                    'id' => $link->id,
                    'type' => $link->type,
                    'label' => $link->label,
                    'url' => $link->url,
                    'is_active' => $link->is_active,
                ])
                ->values()),
        ];
    }

    private function mediaUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
