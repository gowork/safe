<?php declare(strict_types=1);

namespace tests\GW\Safe;

use GW\Safe\SafeAssocArray;
use GW\Safe\SafeRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class SafeRequestTest extends TestCase
{
    public function test_from()
    {
        $request = new Request(
            ['query_param' => 1, 'query_array_param' => ['x' => 5]],
            ['request_param' => 'test'],
            ['attribute_param' => ['abc' => 'def']]
        );
        $safeRequest = SafeRequest::from($request);

        self::assertEquals(1, $safeRequest->query()->int('query_param'));
        self::assertEquals(SafeAssocArray::from(['x' => 5]), $safeRequest->query()->array('query_array_param'));
        self::assertEquals(10, $safeRequest->query()->int('not_existent_param', 10));
        self::assertEquals('test', $safeRequest->post()->string('request_param'));
        self::assertEquals(
            SafeAssocArray::from(['abc' => 'def']),
            $safeRequest->attributes()->array('attribute_param')
        );
    }
}
