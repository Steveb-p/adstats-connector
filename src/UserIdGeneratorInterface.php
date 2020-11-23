<?php
declare(strict_types=1);

namespace Enzode\AdStatsConnector;

interface UserIdGeneratorInterface
{
    public function generateId(): string;
}
