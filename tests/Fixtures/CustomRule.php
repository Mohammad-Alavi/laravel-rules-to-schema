<?php

namespace LaravelRulesToSchema\Tests\Fixtures;

use Illuminate\Contracts\Validation\ValidationRule;

class CustomRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        //
    }
}
