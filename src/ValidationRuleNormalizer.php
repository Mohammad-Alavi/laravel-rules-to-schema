<?php

namespace LaravelRulesToSchema;

use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationRuleParser;
use ReflectionClass;

class ValidationRuleNormalizer
{
    use ParsesNormalizedRuleset;

    public function __construct(
        protected array $rules
    ) {
        $this->rules = $this->standardizeRules($this->rules);
    }

    protected function standardizeRules(array $rawRules): array
    {
        $nestedRules = [];

        foreach ($rawRules as $name => $rules) {

            if (is_string($rules)) {
                $rules = $this->splitStringToRuleset($rules);
            }

            $rules = $this->normalizeRuleset($rules);

            Arr::set($nestedRules, "$name.".config('rules-to-schema.validation_rule_token'), $rules);
        }

        return $nestedRules;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return array<int, array<string|object,array>>
     */
    protected function normalizeRuleset(array $rules): array
    {
        $normalized = [];

        foreach ($rules as $rule) {
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
