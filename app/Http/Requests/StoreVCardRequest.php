<?php

namespace App\Http\Requests;

use App\Models\VCard;
use App\Rules\AlphabeticVCardText;
use App\Rules\PlainVCardText;
use App\Support\SocialLinkHost;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
        $plainText = new PlainVCardText;
        $alphabeticText = new AlphabeticVCardText;

        return [
            'title' => ['required', 'string', 'max:120', $alphabeticText],
            'public_name' => ['required', 'string', 'max:120', $alphabeticText],
            'organization' => ['nullable', 'string', 'max:120', $alphabeticText],
            'job_title' => ['nullable', 'string', 'max:120', $alphabeticText],
            'phone' => ['nullable', 'string', 'max:40', $plainText],
            'public_email' => ['nullable', 'email:rfc', 'max:255'],
            'website' => ['nullable', 'url:http,https', 'max:2048'],
            'description' => ['nullable', 'string', 'max:280', $alphabeticText],
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
            'links.*.label' => ['nullable', 'string', 'max:80', $plainText],
            'links.*.url' => ['nullable', 'url:http,https', 'max:2048'],
            'links.*.is_active' => ['sometimes', 'boolean'],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $links = $this->input('links', []);

            foreach (is_array($links) ? $links : [] as $index => $link) {
                if (is_array($link)
                    && isset($link['type'], $link['url'])
                    && is_string($link['type'])
                    && is_string($link['url'])
                    && $link['url'] !== ''
                    && ! SocialLinkHost::accepts($link['type'], $link['url'])) {
                    $validator->errors()->add("links.{$index}.url", 'El enlace no corresponde a la red social seleccionada.');
                }
            }
        }];
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
