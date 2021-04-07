<?php declare(strict_types=1);

namespace tests\GW\Safe;

use GW\Safe\SafeParameterBag;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;

class SafeParameterBagTest extends TestCase
{
    public function test_InputBag()
    {
        $safeBag = SafeParameterBag::from(new InputBag(['test' => 1, 'array' => ['abc' => 'def']]));

        self::assertEquals(1, $safeBag->value('test', null));
        self::assertEquals(2, $safeBag->value('not-exists', 2));
        self::assertEquals(['abc' => 'def'], $safeBag->value('array', []));
    }
}
