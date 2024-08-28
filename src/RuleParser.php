<?php

namespace LaravelRulesToSchema;

use FluentJsonSchema\Enums\JsonSchemaType;
use FluentJsonSchema\FluentSchema;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Enum as EnumRule;
use Illuminate\Validation\Rules\In as InRule;
use Illuminate\Validation\ValidationRuleParser;
use ReflectionClass;

class RuleParser
{
    const ValidationRuleToken = '##_VALIDATION_RULES_##';

    protected FluentSchema $schema;

    public function __construct(
        protected array $rules
    )
    {
        $this->rules = $this->standardizeRules($this->rules);
    }

    public function standardizeRules(array $rawRules): array
    {
        $nestedRules = [];

        foreach($rawRules as $name => $rules) {

            if (is_string($rules)) {
                $rules = $this->splitStringToRuleset($rules);
            }

            $rules = $this->normalizeRuleset($rules);

            Arr::set($nestedRules, "$name." . self::ValidationRuleToken, $rules);
        }

        return $nestedRules;
    }

    public function parse(): FluentSchema
    {
        $schema = FluentSchema::make()
            ->type()->object()
            ->return();

        foreach($this->rules as $property => $rawRules) {
            $propertySchema = $this->parseRuleset($property, $rawRules);

            if ($propertySchema instanceof FluentSchema) {
                $schema->object()->property($property, $propertySchema);
            } elseif (is_array($propertySchema)) {
                $schema->object()->properties($propertySchema);
            }
        }

        return $schema;
    }

    public function parseRuleset(string $name, array $nestedRuleset): null|FluentSchema|array
    {
        $validationRules = $nestedRuleset[self::ValidationRuleToken] ?? [];

        $schema = FluentSchema::make();

        $this->applyTypeToSchema($schema, $validationRules, $name, $nestedRuleset);

        $this->applyNestedObjectsToSchema($schema, $validationRules, $name, $nestedRuleset);

        $this->applyFormatToSchema($schema, $validationRules);

        $this->applyOtherPropertiesToSchema($schema, $validationRules);

        $this->applyEnumOptionsToSchema($schema, $validationRules);

        $this->applyRequiredToSchema($schema, $validationRules);

        if ($this->checkIfExcluded($validationRules)) {
            return null;
        }

        if ($this->checkIfConfirmed($validationRules)) {
            return [
                $name               => $schema,
                "{$name}_confirmed" => $schema,
            ];
        }

        return $schema;
    }

    protected function applyTypeToSchema(FluentSchema $schema, array $rules, string $name, array $ruleset): void
    {
        foreach($rules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;

            $ruleName = is_object($rule) ? get_class($rule) : $rule;

            if (in_array($ruleName, RuleCategory::strings())) {
                $schema->type()->string();
            }
            if (in_array($ruleName, RuleCategory::integers())) {
                $schema->type()->integer();
            }
            if (in_array($ruleName, RuleCategory::numbers())) {
                $schema->type()->number();
            }
            if (in_array($ruleName, RuleCategory::booleans())) {
                $schema->type()->boolean();
            }
            if (in_array($ruleName, RuleCategory::arrays())) {
                // Check if what we are dealing with is not an object type with properties
                if (count(array_diff_key($ruleset, array_flip([self::ValidationRuleToken]))) == 0) {
                    $schema->type()->array();
                }
            }
            if (in_array($ruleName, RuleCategory::nullables())) {
                $schema->type()->null();
            }

            if ($rule instanceof EnumRule) {
                $enumType = invade($rule)->type;

                $reflection = new ReflectionClass($enumType);

                if (
                    $reflection->implementsInterface(\BackedEnum::class)
                    && count($reflection->getConstants()) > 0
                ) {
                    $value = array_values($reflection->getConstants())[0]->value;

                    if (is_string($value)) {
                        $schema->type()->string();
                    }
                    if (is_int($value)) {
                        $schema->type()->integer();
                    }
                }
            }

            if ($rule instanceof InRule || $rule === 'in') {
                if (is_string($rule)) {
                    $values = array_map(function(mixed $value) {
                        if (is_numeric($value)) {
                            if (ctype_digit($value)) {
                                return intval($value);
                            }
                            return floatval($value);
                        }
                        return $value;
                    }, $args);
                } else {
                    $values = invade($rule)->values;
                }

                $isString  = true;
                $isInt     = true;
                $isNumeric = true;

                foreach($values as $value) {
                    if (is_string($value)) {
                        $isString  = $isString && true;
                        $isInt     = false;
                        $isNumeric = false;
                    }

                    if (is_int($value)) {

                        $isString  = false;
                        $isInt     = $isInt && true;
                        $isNumeric = false;
                    }

                    if (is_float($value)) {
                        $isString  = false;
                        $isInt     = false;
                        $isNumeric = $isNumeric && true;
                    }
                }

                if ($isString) {
                    $schema->type()->string();
                }
                if ($isInt) {
                    $schema->type()->integer();
                }
                if ($isNumeric) {
                    $schema->type()->number();
                }
            }
        }
    }

    protected function applyNestedObjectsToSchema(FluentSchema $schema, array $rules, string $name, array $ruleset): void
    {
        $nestedObjects = array_filter($ruleset, fn($x) => $x != self::ValidationRuleToken, ARRAY_FILTER_USE_KEY);

        if (count($nestedObjects) > 0) {
            $isArray = array_key_exists('*', $nestedObjects);

            if ($isArray) {
                $objSchema = $this->parseRuleset("$name.*", $nestedObjects['*']);

                $schema->type()->array()
                    ->items($objSchema);
            } else {
                foreach($nestedObjects as $propName => $objValidationRules) {
                    $schema->type()->object()
                        ->property($propName, $this->parseRuleset($propName, $objValidationRules));
                }
            }
        }
    }

    protected function checkIfExcluded(array $rules): bool
    {
        foreach($rules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;

            if (is_string($rule) && in_array($rule, RuleCategory::excluded())) {
                return true;
            }
        }

        return false;
    }

    protected function checkIfConfirmed(array $rules): bool
    {
        foreach($rules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;

            if ($rule === 'confirmed') {
                return true;
            }
        }

        return false;
    }

    protected function applyRequiredToSchema(FluentSchema $schema, array $rules): void
    {
        $foundRequired = false;
        foreach($rules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;

            if (!is_string($rule)) {
                continue;
            }

            if ($rule === 'sometimes') {
                return;
            } elseif ($rule == 'required') {
                $foundRequired = true;
            }
        }

        if ($foundRequired) {
            $schema->object()->required();
        }
    }

    protected function applyFormatToSchema(FluentSchema $schema, array $rules): void
    {
        foreach($rules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;

            // Only enabling formatting for supported rules
            // see https://laravel.com/docs/11.x/validation#available-validation-rules

            // Dates are not enabled because Laravel doesn't differentiate between dates and date-times

            match ($rule) {
//                'regex', 'not_regex' => $schema->format()->regex(),
//                'json-pointer'                       => $schema->format()->jsonPointer(),
//                'relative-json-pointer'              => $schema->format()->relativeJsonPointer(),
//                'uri-template'                       => $schema->format()->uriTemplate(),
                'uuid'  => $schema->format()->uuid(),
//                'iri-reference'                      => $schema->format()->iriReference(),
//                'iri'                                => $schema->format()->iri(),
//                'uri-reference'                      => $schema->format()->uriReference(),
//                'uri'                => $schema->format()->uri(),
                'url'   => $schema->format()->uri(),
                'ipv4'  => $schema->format()->ipv4(),
                'ipv6'  => $schema->format()->ipv6(),
//                'hostname'                           => $schema->format()->hostname(),
//                'idn-hostname'                       => $schema->format()->idnHostname(),
                'email' => $schema->format()->email(),
//                'idn-email'                          => $schema->format()->idnEmail(),
//                'date-time'                          => $schema->format()->dateTime(),
//                'date', 'date_format', 'date_equals' => $schema->format()->dateTime(),
//                'time'                               => $schema->format()->time(),
//                'duration'                           => $schema->format()->duration(),
                default => null,
            };

            // TODO: Also validate mimes file extensions somehow?
            if ($rule == 'mimetypes' && count($args) > 0) {
                // TODO: What to do about the rest of the specified mime types
                $schema->content()->mediaType($args[0]);
            }
        }
    }

    protected function applyOtherPropertiesToSchema(FluentSchema $schema, array $rules): void
    {
        /** @var JsonSchemaType[] $schemaTypes */
        $schemaTypes = $schema->getSchemaDTO()->type;

        foreach($rules as $ruleArgs) {
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
    }

    protected function applyEnumOptionsToSchema(FluentSchema $schema, array $rules): void
    {
        foreach($rules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;

            if ($rule instanceof EnumRule) {
                $enumType = invade($rule)->type;

                $reflection = new ReflectionClass($enumType);

                if (count($reflection->getConstants()) > 0) {
                    $values = array_values(array_map(function(\UnitEnum|\BackedEnum $c) {
                        return $c instanceof \BackedEnum ? $c->value : $c->name;
                    }, $reflection->getConstants()));

                    $schema->object()->enum($values);
                }
            }
        }
    }

    /**
     * Utility Functions
     */

    /**
     * @param array $rules
     * @return array<string, array>
     */
    protected function normalizeRuleset(array $rules): array
    {
        $normalized = [];

        foreach($rules as $rule) {
            if (is_string($rule)) {
                $result       = $this->parseStringRuleArgs($rule);
                $normalized[] = $result;
            } else {
                $normalized[] = [$rule, null];
            }
        }

        return $normalized;
    }

    protected function splitStringToRuleset(string $rules): array
    {
        $instance = new ValidationRuleParser([]);
        $class    = new ReflectionClass($instance);
        $method   = $class->getMethod('explodeExplicitRule');
        $method->setAccessible(true);

        return $method->invokeArgs($instance, [$rules, null]);
    }

    protected function parseStringRuleArgs(string $rule): array
    {
        $instance = new ValidationRuleParser([]);
        $class    = new ReflectionClass($instance);
        $method   = $class->getMethod('parseParameters');
        $method->setAccessible(true);

        $parameters = [];

        if (str_contains($rule, ':')) {
            [$rule, $parameter] = explode(':', $rule, 2);

            $parameters = $method->invokeArgs($instance, [$rule, $parameter]);
        }

        return [trim($rule), $parameters];
    }
}
