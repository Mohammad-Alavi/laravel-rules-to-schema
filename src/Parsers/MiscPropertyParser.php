<?php

namespace LaravelRulesToSchema\Parsers;

use FluentJsonSchema\FluentSchema;

class MiscPropertyParser  implements \LaravelRulesToSchema\Contracts\RuleParser
{

    public function __invoke(string $property, FluentSchema $schema, array $validationRules, array $nestedRuleset,)
    {
        // TODO: Implement __invoke() method.
    }
}
