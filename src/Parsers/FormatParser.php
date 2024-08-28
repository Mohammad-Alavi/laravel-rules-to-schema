<?php

namespace LaravelRulesToSchema\Parsers;

use FluentJsonSchema\FluentSchema;
use LaravelRulesToSchema\Contracts\RuleParser;

class FormatParser implements RuleParser
{
    public function __invoke(string $property, FluentSchema $schema, array $validationRules, array $nestedRuleset): array|FluentSchema|null
    {
        foreach ($validationRules as $ruleArgs) {
            [$rule, $args] = $ruleArgs;

            // Only enabling formatting for supported rules
            // see https://laravel.com/docs/11.x/validation#available-validation-rules

            // Dates are not enabled because Laravel doesn't differentiate between dates and date-times

            match ($rule) {
                //                'regex', 'not_regex' => $schema->format()->regex(),
                //                'json-pointer'                       => $schema->format()->jsonPointer(),
                //                'relative-json-pointer'              => $schema->format()->relativeJsonPointer(),
                //                'uri-template'                       => $schema->format()->uriTemplate(),
                'uuid' => $schema->format()->uuid(),
                //                'iri-reference'                      => $schema->format()->iriReference(),
                //                'iri'                                => $schema->format()->iri(),
                //                'uri-reference'                      => $schema->format()->uriReference(),
                //                'uri'                => $schema->format()->uri(),
                'url' => $schema->format()->uri(),
                'ipv4' => $schema->format()->ipv4(),
                'ipv6' => $schema->format()->ipv6(),
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

        return $schema;
    }
}
