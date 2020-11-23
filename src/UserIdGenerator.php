<?php
declare(strict_types=1);

namespace Enzode\AdStatsConnector;

class UserIdGenerator implements UserIdGeneratorInterface
{
    public function generateId(): string
    {
        return 'A' . bin2hex(openssl_random_pseudo_bytes(16));
    }
}
