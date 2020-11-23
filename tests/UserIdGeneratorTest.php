<?php

namespace Enzode\AdStatsConnector\Tests;

use Enzode\AdStatsConnector\UserIdGenerator;
use PHPUnit\Framework\TestCase;

class UserIdGeneratorTest extends TestCase
{
    /**
     * @covers \Enzode\AdStatsConnector\UserIdGenerator::generateId
     */
    public function testGenerateCreatesIdFollowingAPattern(): void
    {
        $generator = new UserIdGenerator();
        $id = $generator->generateId();

        $this->assertMatchesRegularExpression('~^A[a-z0-9]{32}$~', $id);
    }
}
