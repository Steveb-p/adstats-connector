<?php

namespace Enzode\AdStatsConnector;

class PageInfo
{
    private $pageUrl;
    private $pageId;
    private $pageType;

    public function __construct($pageUrl = null, $pageId = null, $pageType = null)
    {
        $this->pageUrl = $pageUrl;
        $this->pageId = $pageId;
        $this->pageType = $pageType;
    }

    public static function fromReferer(?string $referer = null): self
    {
        return new self($referer);
    }

    public static function fromPageData(array $data): self
    {
        $self = new self($data['domain'] . $data['url'], $data['id'], $data['type']);

        return $self;
    }
}