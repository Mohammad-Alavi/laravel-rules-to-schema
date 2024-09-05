<?php

namespace LaravelRulesToSchema;

use FluentJsonSchema\FluentSchema;
use Illuminate\Foundation\Http\FormRequest;

class LaravelRulesToSchema
{
    use ParsesNormalizedRuleset;

    protected static array $additionalParsers = [];

    protected static array $additionalCustomSchemas = [];

    public function parse(string|array $rules): FluentSchema
    {
        if (is_string($rules)) {
            if (! class_exists($rules)) {
                throw new \Exception("Class $rules does not implement ".FormRequest::class.' and can not be parsed.');
            }
            $instance = new $rules;

            $rules = method_exists($instance, 'rules') ? app()->call([$instance, 'rules']) : [];
        }

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
