<?php

namespace LaravelRulesToSchema\Tests\Fixtures;

use FluentJsonSchema\FluentSchema;
use Illuminate\Contracts\Validation\ValidationRule;
use LaravelRulesToSchema\Contracts\HasJsonSchema;

class CustomRuleWithSchema implements HasJsonSchema, ValidationRule
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

    public function toJsonSchema(string $attribute): FluentSchema
    {
        return FluentSchema::make()
            ->type()->array()
            ->items(FluentSchema::make()
                ->type()->integer()
            )
            ->return();
    }
}
