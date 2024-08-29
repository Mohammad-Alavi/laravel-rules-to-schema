<?php

namespace LaravelRulesToSchema;

class LaravelRuleType
{
    public static function string(): array
    {
        return [
            'string',
            'password',
            'date',
            'date_format',
            'date_equals',
            'alpha',
            'alpha_dash',
            'alpha_num',
            'ip',
            'ipv4',
            'ipv6',
            'mac_address',
            'json',
            'url',
            'uuid',
            'ulid',
            'regex',
            'not_regex',
            'email',
        ];
    }

    public static function integer(): array
    {
        return [
            'integer',
            'int',
            'digits',
            'digits_between',
        ];
    }

    public static function number(): array
    {
        return [
            'numeric',
            'decimal',
        ];
    }

    public static function boolean(): array
    {
        return [
            'bool',
            'boolean',
            // Possibly accepted/declined
        ];
    }

    public static function array(): array
    {
        return [
            'array',
            'list',
        ];
    }

    public static function nullable(): array
    {
        return [
            'nullable',
        ];
    }

    public static function conditional(): array
    {
        return [
            'sometimes',
            'required_if',
            'required_unless',
            'required_with',
            'required_without',
            'exclude_if',
            'exclude_unless',
            'exclude_with',
            'exclude_without',
            'missing_if',
            'missing_unless',
            'missing_with',
            'missing_without',
            'prohibited_if',
            'prohibited_unless',
            'prohibited_with',
            'prohibited_without',
            \Illuminate\Validation\Rules\RequiredIf::class,
            \Illuminate\Validation\Rules\ProhibitedIf::class,
            \Illuminate\Validation\Rules\ExcludeIf::class,
        ];
    }

    public static function exclude(): array
    {
        return [
            'prohibited',
            'missing',
            'exclude',
        ];
    }
}
