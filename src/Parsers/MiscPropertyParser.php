<?php

namespace LaravelRulesToSchema\Parsers;

use FluentJsonSchema\Enums\JsonSchemaType;
use FluentJsonSchema\FluentSchema;

class MiscPropertyParser  implements \LaravelRulesToSchema\Contracts\RuleParser
{

    public function __invoke(string $property, FluentSchema $schema, array $validationRules, array $nestedRuleset,): array|FluentSchema|null
    {
        /** @var JsonSchemaType[] $schemaTypes */
        $schemaTypes = $schema->getSchemaDTO()->type;

        foreach($validationRules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;

            foreach($schemaTypes ?? [] as $type) {
                if ($type === JsonSchemaType::STRING) {
                    if ($rule === 'min' && count($args) > 0) {
                        $schema->string()->minLength($args[0]);
                    } elseif ($rule === 'max' && count($args) > 0) {
                        $schema->string()->maxLength($args[0]);
                    }
                } elseif (in_array($type, [JsonSchemaType::INTEGER, JsonSchemaType::NUMBER])) {
                    if ($rule === 'min' && count($args) > 0) {
                        $schema->number()->minimum($args[0]);
                    } elseif ($rule === 'max' && count($args) > 0) {
                        $schema->number()->maximum($args[0]);
                    }
                } elseif ($type === JsonSchemaType::ARRAY) {
                    if ($rule === 'min' && count($args) > 0) {
                        $schema->array()->minItems($args[0]);
                    } elseif ($rule === 'max' && count($args) > 0) {
                        $schema->array()->maxItems($args[0]);
                    }
                }
            }

            if ($rule === 'regex' && count($args) > 0) {

                $matched = preg_match('/^(.)(.*?)\1[a-zA-Z]*$/', $args[0], $matches);

                if ($matched) {
                    $schema->string()->pattern($matches[2]);
                }
            }
        }

        return $schema;
    }
}
