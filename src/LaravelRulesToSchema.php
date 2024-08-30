<?php

namespace LaravelRulesToSchema;

use FluentJsonSchema\FluentSchema;

class LaravelRulesToSchema
{
    use ParsesNormalizedRuleset;

    protected static array $additionalParsers = [];

    protected static array $additionalCustomSchemas = [];

    public function parse(array $rules): FluentSchema
    {
        $normalizedRules = (new ValidationRuleNormalizer($rules))->getRules();

        $schema = FluentSchema::make()
            ->type()->object()
            ->return();

        foreach ($normalizedRules as $property => $rawRules) {
            $propertySchema = $this->parseRuleset($property, $rawRules);

            if ($propertySchema instanceof FluentSchema) {
                $schema->object()->property($property, $propertySchema);
            } elseif (is_array($propertySchema)) {
                $schema->object()->properties($propertySchema);
            }
        }

        return $schema;
    }

    public function getParsers(): array
    {
        return array_merge(
            config('rules-to-schema.parsers'),
            self::$additionalParsers,
        );
    }

    public function registerParser(string $parser): void
    {
        self::$additionalParsers[] = $parser;
    }

    public function getCustomRuleSchemas(): array
    {

        return array_merge(
            config('rules-to-schema.custom_rule_schemas'),
            self::$additionalCustomSchemas,
        );
    }

    public function registerCustomRuleSchema(string $rule, mixed $type): void
    {
        self::$additionalCustomSchemas[$rule] = $type;
    }
}
