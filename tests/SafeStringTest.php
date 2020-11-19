<?php

namespace tests\GW\Safe;

use GW\Safe\SafeAssocArray;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use function tmpfile;
use function uniqid;

class SafeStringTest extends TestCase
{
    /** @dataProvider possibleStringValues */
    function test_casts_possible_string_values($value, string $expected)
    {
        self::assertEquals($expected, SafeAssocArray::from(['string' => $value])->string('string'));
        self::assertEquals($expected, SafeAssocArray::from(['string' => $value])->stringNullable('string'));
    }

    /** @dataProvider impossibleStringValues */
    function test_throwing_InvalidArgumentException_on_value_that_cannot_be_string($notString)
    {
        $this->expectException(InvalidArgumentException::class);
        SafeAssocArray::from(['value' => $notString])->string('value');
    }

    /** @dataProvider impossibleStringValues */
    function test_throwing_InvalidArgumentException_on_value_that_cannot_be_stringNullable($notString)
    {
        $this->expectException(InvalidArgumentException::class);
        SafeAssocArray::from(['value' => $notString])->stringNullable('value');
    }

    function test_throwing_InvalidArgumentException_on_null()
    {
        $this->expectException(InvalidArgumentException::class);
        SafeAssocArray::from(['value' => null])->string('value');
    }

    /** @dataProvider impossibleStringValues */
    function test_returning_default_when_value_is_not_string_like($notString)
    {
        $safe = SafeAssocArray::from(['value' => $notString]);
        $string = uniqid();

        self::assertSame($string, $safe->stringOrDefault('value', $string));
    }

    function test_casts_array_of_values_that_can_be_string()
    {
        self::assertEquals(
            ['John', '123', '1.23', '1', '0', 'kind of string'],
            SafeAssocArray::from(
                [
                    'strings' => [
                        'John',
                        123,
                        1.23,
                        true,
                        false,
                        new StringObject('kind of string'),
                    ],
                ]
            )->strings('strings')
        );
    }

    function test_throws_InvalidArgumentException_when_cannot_cast_array_of_strings()
    {
        $this->expectException(InvalidArgumentException::class);
        SafeAssocArray::from(['notOnlyStrings' => ['John', new stdClass()]])->strings('notOnlyStrings');
    }

    function test_filtered_casting_only_strings_from_array_of_mixed_values()
    {
        self::assertEquals(
            ['John', 'kind of string'],
            SafeAssocArray::from(
                [
                    'strings' => [
                        'John',
                        ['array'],
                        new StringObject('kind of string'),
                        null,
                    ],
                ]
            )->stringsFiltered('strings')
        );
    }

    function test_casting_array_of_mixed_values_with_provided_default()
    {
        self::assertEquals(
            ['John', 'default', 'kind of string', 'default'],
            SafeAssocArray::from(
                [
                    'strings' => [
                        'John',
                        ['array'],
                        new StringObject('kind of string'),
                        null,
                    ],
                ]
            )->stringsForced('strings', 'default')
        );
    }

    public function possibleStringValues(): array
    {
        return [
            ['string', 'string'],
            [123, '123'],
            [1.23, '1.23'],
            [true, '1'],
            [false, '0'],
            [new StringObject('kind of string'), 'kind of string'],
        ];
    }

    public function impossibleStringValues(): array
    {
        return [
            [['array']],
            [tmpfile()],
        ];
    }
}
