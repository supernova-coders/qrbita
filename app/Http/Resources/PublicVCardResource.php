<?php

namespace App\Http\Resources;

use App\Models\VCard;
use App\Models\VCardLink;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin VCard */
class PublicVCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'public_name' => $this->public_name,
            'organization' => $this->organization,
            'job_title' => $this->job_title,
            'phone' => $this->phone,
            'public_email' => $this->public_email,
            'website' => $this->website,
            'description' => $this->description,
            'photo_url' => $this->mediaUrl($this->photo_path),
            'cover_url' => $this->mediaUrl($this->cover_path),
            'logo_url' => $this->mediaUrl($this->logo_path),
            'contact_url' => route('public.vcards.contact', $this->resource),
            'links' => $this->whenLoaded('links', fn () => $this->links
                ->map(fn (VCardLink $link) => [
                    'type' => $link->type,
                    'label' => $link->label,
                    'redirect_url' => route('public.vcards.links.redirect', [
                        'vCard' => $this->resource,
                        'link' => $link,
                    ]),
                ])
                ->values()),
        ];
    }

    private function mediaUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
