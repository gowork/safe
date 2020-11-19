<?php

namespace tests\GW\Safe;

use GW\Safe\SafeAssocArray;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use function random_int;
use const M_PI;

class SafeFloatTest extends TestCase
{
    /** @dataProvider possibleFloatValues */
    function test_casting_possible_float_values($value, float $expected)
    {
        self::assertEquals($expected, SafeAssocArray::from(['float' => $value])->float('float'));
        self::assertEquals($expected, SafeAssocArray::from(['float' => $value])->floatNullable('float'));
        self::assertEquals($expected, SafeAssocArray::from(['float' => $value])->floatOrNull('float'));
        self::assertEquals($expected, SafeAssocArray::from(['float' => $value])->floatOrDefault('float', M_PI));
    }

    /** @dataProvider impossibleFloatValues */
    function test_throwing_InvalidArgumentException_on_value_that_cannot_be_float($notFloat)
    {
        $this->expectException(InvalidArgumentException::class);
        SafeAssocArray::from(['value' => $notFloat])->float('value');
    }

    /** @dataProvider impossibleFloatValues */
    function test_throwing_InvalidArgumentException_on_value_that_cannot_be_float_nullable($notFloat)
    {
        $this->expectException(InvalidArgumentException::class);
        SafeAssocArray::from(['value' => $notFloat])->floatNullable('value');
    }

    /** @dataProvider impossibleFloatValues */
    function test_floatOrNull_returns_null_on_value_that_cannot_be_number($notFloat)
    {
        self::assertNull(SafeAssocArray::from(['value' => $notFloat])->floatOrNull('value'));
    }

    function test_throwing_InvalidArgumentException_on_null()
    {
        $this->expectException(InvalidArgumentException::class);
        SafeAssocArray::from(['value' => null])->float('value');
    }

    /** @dataProvider impossibleFloatValues */
    function test_returning_default_when_value_is_not_numeric($notFloat)
    {
        $safe = SafeAssocArray::from(['value' => $notFloat]);
        $float = (float)(random_int(100, 200) / random_int(1, 100));

        self::assertSame($float, $safe->floatOrDefault('value', $float));
    }

    function test_casts_array_of_values_that_can_be_float()
    {
        self::assertEquals(
            [123.0, 1.23, 1.0, 0.0, 123.99],
            SafeAssocArray::from(
                [
                    'floats' => [
                        123,
                        1.23,
                        true,
                        false,
                        new StringObject('123.99'),
                    ],
                ]
            )->floats('floats')
        );
    }

    function test_throws_InvalidArgumentException_when_cannot_cast_array_of_floats()
    {
        $this->expectException(InvalidArgumentException::class);
        SafeAssocArray::from(['notOnlyFloats' => ['123', new stdClass()]])->floats('notOnlyFloats');
    }

    function test_filtered_casting_only_floats_from_array_of_mixed_values()
    {
        self::assertEquals(
            [123.99, 456.99],
            SafeAssocArray::from(
                [
                    'floats' => [
                        '123.99',
                        ['array'],
                        new StringObject('456.99'),
                        null,
                    ],
                ]
            )->floatsFiltered('floats')
        );
    }

    function test_casting_array_of_mixed_values_with_provided_default()
    {
        $safe = SafeAssocArray::from(
            [
                'floats' => [
                    '123.99',
                    ['array'],
                    new StringObject('456.99'),
                    null,
                ],
            ]
        );

        self::assertEquals([123.99, 0.0, 456.99, 0.0], $safe->floatsForced('floats'));
        self::assertEquals([123.99, 42.42, 456.99, 42.42], $safe->floatsForced('floats', 42.42));
    }

    public function possibleFloatValues(): array
    {
        return [
            [123, 123.0],
            [1.23, 1.23],
            [true, 1.0],
            [false, 0.0],
            [new StringObject('123.99'), 123.99],
        ];
    }

    public function impossibleFloatValues(): array
    {
        return [
            [['array']],
            ['string'],
            [new StringObject('string')],
        ];
    }
}
