<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PlainVCardText implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        if (preg_match('/[<>`]|[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]|[\x{202A}-\x{202E}\x{2066}-\x{2069}]/u', $value)
            || preg_match('/\b(?:javascript|data|vbscript)\s*:/iu', $value)) {
            $fail('El campo :attribute solo puede contener texto de la vCard, sin código ni contenido activo.');
        }
    }
}
