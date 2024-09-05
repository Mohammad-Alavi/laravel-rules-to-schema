<?php

namespace LaravelRulesToSchema\Facades;

use FluentJsonSchema\FluentSchema;
use Illuminate\Support\Facades\Facade;

/**
 * @method static FluentSchema parse(string|array $rules) Parse Rules
 * @method static void registerParser(string $parser) Register a rule parser
 * @method static void registerCustomRuleSchema(string $rule, mixed $type) Register a schema for a custom rule
 *
 * @see \LaravelRulesToSchema\LaravelRulesToSchema
 */
class LaravelRulesToSchema extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \LaravelRulesToSchema\LaravelRulesToSchema::class;
    }
}
