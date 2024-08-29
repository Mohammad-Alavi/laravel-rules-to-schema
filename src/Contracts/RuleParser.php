<?php

namespace LaravelRulesToSchema\Contracts;

use FluentJsonSchema\FluentSchema;

interface RuleParser
{
    /**
     * @return null|FluentSchema|FluentSchema[]
     */
    public function __invoke(
        string $attribute,
        FluentSchema $schema,
        array $validationRules,
        array $nestedRuleset,
    ): array|FluentSchema|null;
}
