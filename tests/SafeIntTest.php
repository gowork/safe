<?php

namespace tests\GW\Safe;

use GW\Safe\SafeAssocArray;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use function random_int;
use function uniqid;

class SafeIntTest extends TestCase
{
    /** @dataProvider possibleIntegerValues */
    function test_casting_possible_int_values($value, int $expected)
    {
        self::assertEquals($expected, SafeAssocArray::from(['int' => $value])->int('int'));
        self::assertEquals($expected, SafeAssocArray::from(['int' => $value])->intNullable('int'));
    }

    /** @dataProvider impossibleIntegerValues */
    function test_throwing_InvalidArgumentException_on_value_that_cannot_be_int($notInt)
    {
        $this->expectException(InvalidArgumentException::class);
        SafeAssocArray::from(['value' => $notInt])->int('value');
    }

    /** @dataProvider impossibleIntegerValues */
    function test_throwing_InvalidArgumentException_on_value_that_cannot_be_int_nullable($notInt)
    {
        $this->expectException(InvalidArgumentException::class);
        SafeAssocArray::from(['value' => $notInt])->intNullable('value');
    }

    function test_throwing_InvalidArgumentException_on_null()
    {
        $this->expectException(InvalidArgumentException::class);
        SafeAssocArray::from(['value' => null])->int('value');
    }

    /** @dataProvider impossibleIntegerValues */
    function test_returning_default_when_value_is_not_numeric($notInt)
    {
        $safe = SafeAssocArray::from(['value' => $notInt]);
        $int = random_int(0, 1000000);

        self::assertSame($int, $safe->intOrDefault('value', $int));
    }

    function test_casts_array_of_values_that_can_be_int()
    {
        self::assertEquals(
            [123, 1, 1, 0, 123],
            SafeAssocArray::from(
                [
                    'ints' => [
                        123,
                        1.23,
                        true,
                        false,
                        new StringObject('123'),
                    ],
                ]
            )->ints('ints')
        );
    }

    function test_throws_InvalidArgumentException_when_cannot_cast_array_of_ints()
    {
        $this->expectException(InvalidArgumentException::class);
        SafeAssocArray::from(['notOnlyInts' => ['123', new stdClass()]])->ints('notOnlyInts');
    }

    function test_filtered_casting_only_ints_from_array_of_mixed_values()
    {
        self::assertEquals(
            [123, 456],
            SafeAssocArray::from(
                [
                    'ints' => [
                        '123',
                        ['array'],
                        new StringObject('456'),
                        null,
                    ],
                ]
            )->intsFiltered('ints')
        );
    }

    function test_casting_array_of_mixed_values_with_provided_default()
    {
        self::assertEquals(
            [123, 42, 456, 42],
            SafeAssocArray::from(
                [
                    'ints' => [
                        '123',
                        ['array'],
                        new StringObject('456'),
                        null,
                    ],
                ]
            )->intsForced('ints', 42)
        );
    }

    public function possibleIntegerValues(): array
    {
        return [
            [123, 123],
            [1.23, 1],
            [true, 1],
            [false, 0],
            [new StringObject('123'), 123],
        ];
    }

    public function impossibleIntegerValues(): array
    {
        return [
            [['array']],
            ['string'],
            [new StringObject('string')],
        ];
    }
}
