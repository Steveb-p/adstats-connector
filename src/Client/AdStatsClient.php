<?php
declare(strict_types=1);

namespace Enzode\AdStatsConnector\Client;

use Enzode\AdStatsConnector\DataTransferObject\GenericEvent;
use Enzode\AdStatsConnector\DataTransferObject\Hit;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AdStatsClient
{
    private const ENDPOINT_HIT = '/api/add_hits';
    private const ENDPOINT_GENERIC = '/api/add_generic';

    /**
     * @var HttpClientInterface
     */
    private $client;

    public static function createDefault(string $endpoint, string $key): self
    {
        $client = HttpClient::createForBaseUri($endpoint, [
            'headers' => [
                'X-AdStats-Apikey' => $key,
                'Accept' => 'application/json',
                'content-type' => 'application/json',
            ],
        ]);

        return new self($client);
    }

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param Hit[] $hits
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function sendHits(array $hits): void
    {
        $this->doSend(self::ENDPOINT_HIT, array_map(static function ($hit): array {
            if ($hit instanceof Hit) {
                return $hit->toArray();
            }

            if (is_array($hit)) {
                return $hit;
            }

            throw new \InvalidArgumentException(sprintf(
                'Invalid argument passed to %s. Only %s and arrays are expected.',
                __METHOD__,
                Hit::class
            ));
        }, $hits));
    }

    /**
     * @param GenericEvent[] $events
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function sendGenericEvents(array $events): void
    {
        $this->doSend(self::ENDPOINT_GENERIC, array_map(static function (GenericEvent $event): array {
            if ($event instanceof GenericEvent) {
                return $event->toArray();
            }

            if (is_array($event)) {
                return $event;
            }

            throw new \InvalidArgumentException(sprintf(
                'Invalid argument passed to %s. Only %s and arrays are expected.',
                __METHOD__,
                GenericEvent::class
            ));
        }, $events));
    }

    /**
     * @param string $endpoint
     * @param array $aData
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function doSend(string $endpoint, array $aData): void
    {
        if ($endpoint !== self::ENDPOINT_HIT && $endpoint !== self::ENDPOINT_GENERIC) {
            throw new \InvalidArgumentException(sprintf('Only "%s" events are allowed', join('","', [
                self::ENDPOINT_HIT,
                self::ENDPOINT_GENERIC,
            ])));
        }

        $this->client->request('POST', $endpoint, [
            'body' => $this->safeJsonEncode($aData),
        ])->getContent();
    }

    /**
     * @throws \JsonException
     */
    private function safeJsonEncode($value): string
    {
        $encoded = json_encode($value);
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $encoded;
            case JSON_ERROR_DEPTH:
                throw self::createJsonException('Maximum stack depth exceeded');
            case JSON_ERROR_STATE_MISMATCH:
                throw self::createJsonException('Underflow or the modes mismatch');
            case JSON_ERROR_CTRL_CHAR:
                throw self::createJsonException('Unexpected control character found');
            case JSON_ERROR_SYNTAX:
                throw self::createJsonException('Syntax error, malformed JSON');
            case JSON_ERROR_UTF8:
                $clean = self::utf8ize($value);
                return $this->safeJsonEncode($clean);
            default:
                throw self::createJsonException('Unknown error in safe_json_encode');
        }
    }

    private static function utf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                // unset the original, replace with proper key
                $newKey = self::utf8ize($key);
                if ($newKey !== $key) {
                    unset($mixed[$key]);
                }
                $mixed[$newKey] = self::utf8ize($value);
            }
        } else if (is_string($mixed)) {
            return utf8_encode($mixed);
        }
        return $mixed;
    }

    /**
     * @param string $message
     *
     * @return \JsonException|\RuntimeException
     */
    private static function createJsonException(string $message): \Exception
    {
        if (class_exists('JsonException')) {
            return new \JsonException($message);
        }

        return new \RuntimeException($message);
    }
}
