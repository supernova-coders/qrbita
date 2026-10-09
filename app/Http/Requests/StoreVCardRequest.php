<?php

namespace App\Http\Requests;

use App\Models\VCard;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', VCard::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'public_name' => ['required', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:120'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'public_email' => ['nullable', 'email:rfc', 'max:255'],
            'website' => ['nullable', 'url:http,https', 'max:2048'],
            'description' => ['nullable', 'string', 'max:280'],
            'accent_color' => ['nullable', Rule::in(['#000000'])],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['sometimes', 'boolean'],
            'remove_cover' => ['sometimes', 'boolean'],
            'remove_logo' => ['sometimes', 'boolean'],
            'links' => ['sometimes', 'array', 'max:8'],
            'links.*.type' => [
                'required',
                'string',
                'distinct',
                Rule::in(['facebook', 'instagram', 'linkedin', 'whatsapp', 'tiktok', 'youtube', 'x', 'github']),
            ],
            'links.*.label' => ['nullable', 'string', 'max:80'],
            'links.*.url' => ['nullable', 'url:http,https', 'max:2048'],
            'links.*.is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'título',
            'public_name' => 'nombre público',
            'organization' => 'organización',
            'job_title' => 'puesto',
            'phone' => 'teléfono',
            'public_email' => 'correo público',
            'website' => 'sitio web',
            'description' => 'descripción',
            'photo' => 'fotografía',
            'cover' => 'portada',
            'logo' => 'logotipo',
            'links.*.url' => 'enlace',
        ];
    }
}
