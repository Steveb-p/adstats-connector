<?php
declare(strict_types=1);

namespace Enzode\AdStatsConnector\DataTransferObject;

use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;

class Hit
{

    private $userId;

    private $referer;

    private $userIp;

    private $userAgent;

    private $requestHost;

    private $requestQueryString;

    private $extra;

    private $campaignRef;

    private $advertRef;

    private $pathId;

    private $pageFromId;

    private $pageFromType;

    private $pageFromUrl;

    private $pageToId;

    private $pageToType;

    private $pageToUrl;

    private $getParams;

    private $utmList;

    private $uuid;

    public function __construct(
        string $userId,
        string $campaignRef,
        ?string $advertRef,
        $pathId,
        $pageFromId,
        $pageFromType,
        $pageFromUrl,
        $pageToId,
        $pageToType,
        $pageToUrl,
        $getParams,
        array $utmList,
        array $extra = []
    )
    {
        $this->userId = $userId;
        $this->extra = $extra;

        $this->campaignRef = $campaignRef;
        $this->advertRef = $advertRef;
        $this->pathId = $pathId;
        $this->pageFromId = $pageFromId;
        $this->pageFromType = $pageFromType;
        $this->pageFromUrl = $pageFromUrl;
        $this->pageToId = $pageToId;
        $this->pageToType = $pageToType;
        $this->pageToUrl = $pageToUrl;
        $this->utmList = $utmList;
        $this->getParams = $getParams;

        $this->uuid = (string) Uuid::uuid4();
    }

    public function withRequest(Request $request): self
    {
        $clone = clone $this;
        $clone->userAgent = $request->headers->get('user-agent', '');
        $clone->requestHost = $request->getHost();
        $clone->requestQueryString = $request->getQueryString();
        $clone->userIp = $request->getClientIp();
        $clone->referer = $request->headers->get('x-referer', $request->headers->get('referer', 'Unknown'));

        return $clone;
    }

    public function toArray(): array
    {
        return array_merge($this->extra, [
            'created_at' => time(),
            'cookie_id' => $this->userId,
            'referer' => $this->referer,
            'user_ip' => $this->userIp,
            'user_agent' => $this->userAgent,
            'request' => [
                'host' => $this->requestHost,
                'query_string' => $this->requestQueryString,
            ],
            'ref' => $this->campaignRef,
            'ad_ref' => $this->advertRef,
            'get_params' => $this->getParams,
            'path_id' => $this->pathId,
            'from_id' => $this->pageFromId,
            'from_type' => $this->pageFromType,
            'from_url' => $this->pageFromUrl,
            'to_id' => $this->pageToId,
            'to_type' => $this->pageToType,
            'to_url' => $this->pageToUrl,
            'utm_list' => $this->utmList,
            'uuid' => $this->uuid,
        ]);
    }
}
