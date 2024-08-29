<?php

namespace LaravelRulesToSchema\Contracts;

use FluentJsonSchema\FluentSchema;

interface RuleParser
{
    /**
     * @param string       $property
     * @param FluentSchema $schema
     * @param array        $validationRules
     * @param array        $nestedRuleset
     * @return null|FluentSchema|FluentSchema[]
     */
    public function __invoke(
        string       $property,
        FluentSchema $schema,
        array        $validationRules,
        array        $nestedRuleset,
    ): array|FluentSchema|null;
}
