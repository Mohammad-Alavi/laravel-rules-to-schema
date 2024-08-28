<?php

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use LaravelRulesToSchema\Facades\LaravelRulesToSchema;
use LaravelRulesToSchema\Tests\Fixtures\TestEnum;
use LaravelRulesToSchema\Tests\Fixtures\TestIntBackedEnum;
use LaravelRulesToSchema\Tests\Fixtures\TestModel;
use LaravelRulesToSchema\Tests\Fixtures\TestStringBackedEnum;

test('parse string rules', function () {
    $rules = [
        'strings' => 'string|url|min:6',
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('string types', function () {
    $rules = [
        'string_property' => ['string'],
        'password_property' => ['password'],
        'date_property' => ['date'],
        'alpha_property' => ['alpha'],
        'alpha_dash_property' => ['alpha_dash'],
        'alpha_num_property' => ['alpha_num'],
        'enum_property' => [new Enum(TestStringBackedEnum::class)],
        'ip_property' => ['ip'],
        'ipv4_property' => ['ipv4'],
        'ipv6_property' => ['ipv6'],
        'mac_address_property' => ['mac_address'],
        'json_property' => ['json'],
        'url_property' => ['url'],
        'uuid_property' => ['uuid'],
        'ulid_property' => ['ulid'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('integer types', function () {
    $rules = [
        'integer_property' => ['integer'],
        'int_property' => ['int'],
        'enum_property' => [new Enum(TestIntBackedEnum::class)],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('numeric types', function () {
    $rules = [
        'decimal_property' => ['decimal:2,5'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('boolean types', function () {
    $rules = [
        'boolean_property' => ['boolean'],
        'bool_property' => ['bool'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('nullable types', function () {
    $rules = [
        'nullable_property' => ['nullable', 'string'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('nullable objects and arrays', function () {
    $rules = [
        'nullable_object' => ['nullable'],
        'nullable_object.myProperty' => ['string'],
        'nullable_array' => ['nullable', 'array'],
        'nullable_array.*' => ['string'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('nested object properties', function () {
    $rules = [
        'array.*' => ['string'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('nested objects', function () {
    $rules = [
        'object1.object2.property1' => ['string'],
        'object1.object2.property2' => ['string'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('nested object with array', function () {
    $rules = [
        'object1.object2.*' => ['string'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('nested arrays', function () {
    $rules = [
        'array' => ['array', 'one'],
        'array.*' => ['array', 'two'],
        'array.*.one' => ['array', 'three'],
        'array.*.one.*' => ['string'],
        'array.*.two.*.property1' => ['string'],
        'array.*.two.*.property2' => ['string'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('excluded attributes are not displayed', function () {
    $rules = [
        'prohibited_property' => ['prohibited'],
        'missing_property' => ['missing'],
        'exclude_property' => ['exclude'],
        'other_property' => ['string'],
    ];

    // TODO: These can be prohibited in the schema using not
    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('confirmed rule adds additional field', function () {
    $rules = [
        'password' => ['string', 'confirmed'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('can infer type from in rule', function () {
    $rules = [
        'in_rule' => [Rule::in([1, 2, 3])],
        'in_rule_str' => ['in:a,b,c'],
        'in_mixed' => ['in:1,b,c'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('can infer type from exists rule', function () {
    $rules = [
        'exists_property' => [Rule::exists(TestModel::class)],
        'exists_str_property' => ['exists:test_models,name'],
    ];
})->skip('Need to parse migration/schema doc');

test('required', function () {
    $rules = [
        'required_property' => ['required'],
        'sometimes_required' => ['sometimes', 'required'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('format', function () {
    $rules = [
        'regex' => ['regex'],
        'not_regex' => ['not_regex'],
        'uuid' => ['uuid'],
        'url' => ['url'],
        'ipv4' => ['ipv4'],
        'ipv6' => ['ipv6'],
        'email' => ['email'],
        'date' => ['date'],
        'date_format' => ['date_format'],
        'date_equals' => ['date_equals'],
        'mimetypes' => ['mimetypes:application/json,image/png'],
        'mimes' => ['mimes:png,jpg'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('rules apply other attributes', function () {
    $rules = [
        'str_min' => ['string', 'min:1'],
        'str_max' => ['string', 'max:10'],
        'int_min' => ['int', 'min:1'],
        'int_max' => ['int', 'max:10'],
        'float_min' => ['decimal', 'min:1'],
        'float_max' => ['decimal', 'max:10'],
        'array_min' => ['array', 'min:1'],
        'array_max' => ['array', 'max:10'],

        'regex' => ['regex:/[a-z]{4}/i'],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});

test('enum', function () {
    $rules = [
        'enum' => [new Enum(TestEnum::class)],
        'string_enum' => [new Enum(TestStringBackedEnum::class)],
        'int_enum' => [new Enum(TestIntBackedEnum::class)],
    ];

    expect(LaravelRulesToSchema::parse($rules)->compile())
        ->toMatchSnapshot();
});
