<?php

namespace LaravelRulesToSchema\Facades;

use FluentJsonSchema\FluentSchema;
use Illuminate\Support\Facades\Facade;

/**
 * @method static FluentSchema parse(array $rules) Parse Rules
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
