<?php

namespace Enzode\AdStatsConnector\Tests\DataTransferObject;

use Enzode\AdStatsConnector\DataTransferObject\GenericEvent;
use PHPUnit\Framework\TestCase;

class GenericEventTest extends TestCase
{
    /**
     * @dataProvider castingTestProvider
     * @covers \Enzode\AdStatsConnector\DataTransferObject\GenericEvent::toArray
     */
    public function testCastingToArray(GenericEvent $event, callable $expectation): void
    {
        $expectation($event->toArray());
    }

    public function castingTestProvider(): iterable
    {
        yield [
            new GenericEvent(),
            static function ($data): void {
                self::assertIsArray($data);
            },
        ];
    }
}
