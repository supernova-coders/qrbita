<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class AlphabeticVCardText implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        if (preg_match('/\A[\p{L}\p{M}]+(?: [\p{L}\p{M}]+)*\z/u', $value) !== 1) {
            $fail('El campo :attribute solo puede contener letras y espacios; se permiten tildes y diéresis.');
        }
    }
}
