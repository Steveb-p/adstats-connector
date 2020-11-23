<?php

namespace Enzode\AdStatsConnector\Tests\DataTransferObject;

use Enzode\AdStatsConnector\DataTransferObject\Hit;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

class HitTest extends TestCase
{

    protected function setUp(): void
    {
        ClockMock::register(Hit::class);
        ClockMock::withClockMock(true);
    }

    protected function tearDown(): void
    {
        ClockMock::withClockMock(false);
    }

    /**
     * @dataProvider castingTestProvider
     * @covers \Enzode\AdStatsConnector\DataTransferObject\Hit::toArray
     */
    public function testCastingToArray(Hit $hit, callable $expectation): void
    {
        $expectation($hit->toArray());
    }

    public function castingTestProvider(): iterable
    {
        $hit = new Hit(
            'userId',
            'campaignRef',
            'advertRef',
            'pathId',
            'pageFromId',
            'pageFromType',
            'http://page-from-url/',
            'pageToId',
            'pageToType',
            'http://page-to-url/',
            ['XYZ' => 'test'],
            ['utm_campaign' => 'campaignId']
        );
        yield [
            $hit,
            static function ($data): void {
                self::assertIsArray($data);
                self::assertSame(time(), $data['created_at']);
                self::assertSame('userId', $data['cookie_id']);
                self::assertNull($data['referer']);
                self::assertNull($data['user_ip']);
                self::assertNull($data['user_agent']);
                self::assertIsArray($data['request']);
                self::assertNull($data['request']['host']);
                self::assertNull($data['request']['query_string']);
                self::assertSame('campaignRef', $data['ref']);
                self::assertSame('advertRef', $data['ad_ref']);
                self::assertIsArray($data['get_params']);
                self::assertSame('test', $data['get_params']['XYZ']);
                self::assertSame('pathId', $data['path_id']);
                self::assertSame('pageFromId', $data['from_id']);
                self::assertSame('pageFromType', $data['from_type']);
                self::assertSame('http://page-from-url/', $data['from_url']);
                self::assertSame('pageToId', $data['to_id']);
                self::assertSame('pageToType', $data['to_type']);
                self::assertSame('http://page-to-url/', $data['to_url']);
                self::assertIsArray($data['utm_list']);
                self::assertSame('campaignId', $data['utm_list']['utm_campaign']);
                self::assertIsString($data['uuid']);
            },
        ];
    }
}
