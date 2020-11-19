<?php

namespace tests\GW\Safe;

use GW\Safe\SafeAssocArray;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SafeBoolTest extends TestCase
{
    /** @dataProvider possibleBooleanValues */
    function test_casting_possible_bool_values($value, bool $expected)
    {
        self::assertEquals($expected, SafeAssocArray::from(['bool' => $value])->bool('bool'));
    }

    /** @dataProvider impossibleBooleanValues */
    function test_throwing_InvalidArgumentException_on_value_that_cannot_be_bool($notBool)
    {
        $this->expectException(InvalidArgumentException::class);
        SafeAssocArray::from(['value' => $notBool])->bool('value');
    }

    function test_throwing_InvalidArgumentException_on_null()
    {
        $this->expectException(InvalidArgumentException::class);
        SafeAssocArray::from(['value' => null])->bool('value');
    }

    /** @dataProvider impossibleBooleanValues */
    function test_returning_default_when_value_is_not_bool($notBool)
    {
        $safe = SafeAssocArray::from(['value' => $notBool]);

        self::assertTrue($safe->boolOrDefault('value', true));
        self::assertFalse($safe->boolOrDefault('value', false));
    }

    public function possibleBooleanValues(): array
    {
        return [
            [true, true],
            [false, false],
            [1, true],
            [0, false],
            ['1', true],
            ['0', false],
        ];
    }

    public function impossibleBooleanValues(): array
    {
        return [
            ['YES'],
            ['x'],
            [12],
            [['array']],
            ['string'],
            [new StringObject('string')],
            [null],
        ];
    }
}
