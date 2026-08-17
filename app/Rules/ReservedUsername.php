<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ReservedUsername implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $reserved = collect(config('reserved-usernames', []))
            ->map(fn (string $name) => strtolower($name));

        if ($reserved->contains(strtolower((string) $value))) {
            $fail(__('This username is reserved and cannot be used.'));
        }
    }
}
