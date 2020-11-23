<?php

namespace Enzode\AdStatsConnector\Tests\Client;

use Enzode\AdStatsConnector\Client\AdStatsClient;
use Enzode\AdStatsConnector\DataTransferObject\GenericEvent;
use Enzode\AdStatsConnector\DataTransferObject\Hit;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AdStatsClientTest extends TestCase
{

    /**
     * @covers \Enzode\AdStatsConnector\Client\AdStatsClient::sendHits
     */
    public function testSendHits(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->createMock(ResponseInterface::class));

        $client = new AdStatsClient($httpClient);

        $client->sendHits([
            self::generateMockHit(),
        ]);
    }

    /**
     * @covers \Enzode\AdStatsConnector\Client\AdStatsClient::sendGenericEvents
     */
    public function testSendGenericEvents(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(self::once())
            ->method('request')
            ->willReturn($this->createMock(ResponseInterface::class));

        $client = new AdStatsClient($httpClient);

        $client->sendGenericEvents([
            new GenericEvent(),
        ]);
    }

    private static function generateMockHit(): Hit
    {
        return new Hit(
            'aXXX',
            'campaign-ref',
            'advert-ref',
            '557',
            null,
            null,
            'http://referer/',
            666,
            1,
            'http://medic-reporters.com/some-site',
            [],
            []
        );
    }
}
