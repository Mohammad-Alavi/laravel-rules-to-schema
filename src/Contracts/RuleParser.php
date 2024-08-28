<?php

namespace LaravelRulesToSchema\Contracts;

use FluentJsonSchema\FluentSchema;

interface RuleParser
{
    public function __invoke(
        string       $property,
        FluentSchema $schema,
        array        $validationRules,
        array        $nestedRuleset,
    );
}
