<?php

namespace Tests\Unit;

use App\Branch;
use PHPUnit\Framework\TestCase;

class BranchNotificationEmailTest extends TestCase
{
    public function testAnaBuCaviteUsesTheCaviteEmailAlias()
    {
        $branch = new Branch(['branch' => 'Ana Bu Cavite']);

        $this->assertSame('cavite@ideaserv.com.ph', $branch->notificationEmail());
    }
}
