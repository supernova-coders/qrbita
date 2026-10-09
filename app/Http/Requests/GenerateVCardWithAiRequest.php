<?php

namespace App\Http\Requests;

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
        return [
            'prompt' => ['required', 'string', 'min:20', 'max:2000'],
            'current' => ['sometimes', 'array'],
            'current.title' => ['nullable', 'string', 'max:120'],
            'current.public_name' => ['nullable', 'string', 'max:120'],
            'current.organization' => ['nullable', 'string', 'max:120'],
            'current.job_title' => ['nullable', 'string', 'max:120'],
            'current.phone' => ['nullable', 'string', 'max:40'],
            'current.public_email' => ['nullable', 'email:rfc', 'max:255'],
            'current.website' => ['nullable', 'url:http,https', 'max:2048'],
            'current.description' => ['nullable', 'string', 'max:280'],
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
