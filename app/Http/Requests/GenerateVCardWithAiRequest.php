<?php

namespace App\Http\Requests;

use App\Rules\PlainVCardText;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GenerateVCardWithAiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $plainText = new PlainVCardText;

        return [
            'prompt' => ['required', 'string', 'min:20', 'max:2000'],
            'current' => ['sometimes', 'array'],
            'current.title' => ['nullable', 'string', 'max:120', $plainText],
            'current.public_name' => ['nullable', 'string', 'max:120', $plainText],
            'current.organization' => ['nullable', 'string', 'max:120', $plainText],
            'current.job_title' => ['nullable', 'string', 'max:120', $plainText],
            'current.phone' => ['nullable', 'string', 'max:40', $plainText],
            'current.public_email' => ['nullable', 'email:rfc', 'max:255'],
            'current.website' => ['nullable', 'url:http,https', 'max:2048'],
            'current.description' => ['nullable', 'string', 'max:280', $plainText],
            'current.links' => ['sometimes', 'array', 'max:8'],
            'current.links.*' => ['array:type,label,url'],
            'current.links.*.type' => ['required', 'distinct', 'in:facebook,instagram,linkedin,whatsapp,tiktok,youtube,x,github'],
            'current.links.*.label' => ['required', 'string', 'max:80', $plainText],
            'current.links.*.url' => ['required', 'url:http,https', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['prompt' => 'instrucción para la IA'];
    }
}
