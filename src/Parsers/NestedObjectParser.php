<?php

namespace LaravelRulesToSchema\Parsers;

use FluentJsonSchema\FluentSchema;
use LaravelRulesToSchema\Contracts\RuleParser;
use LaravelRulesToSchema\ParsesNormalizedRuleset;

class NestedObjectParser implements RuleParser
{
    use ParsesNormalizedRuleset;

    public function __invoke(string $attribute, FluentSchema $schema, array $validationRules, array $nestedRuleset): array|FluentSchema|null
    {
        $nestedObjects = array_filter($nestedRuleset, fn ($x) => $x != config('rules-to-schema.validation_rule_token'), ARRAY_FILTER_USE_KEY);

        if (count($nestedObjects) > 0) {
            $isArray = array_key_exists('*', $nestedObjects);

            if ($isArray) {
                $objSchema = $this->parseRuleset("$attribute.*", $nestedObjects['*']);

                $schema->type()->array()
                    ->items($objSchema);
            } else {
                foreach ($nestedObjects as $propName => $objValidationRules) {
                    $schema->type()->object()
                        ->property($propName, $this->parseRuleset($propName, $objValidationRules));
                }
            }
        }

        return $schema;
    }
}
