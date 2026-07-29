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

    public function testCagayanDeOroUsesTheCdoEmailAlias()
    {
        $branch = new Branch(['branch' => 'Cagayan De Oro']);

        $this->assertSame('cdo@ideaserv.com.ph', $branch->notificationEmail());
    }

    public function testLegazpiUsesTheLegaspiEmailAlias()
    {
        $branch = new Branch(['branch' => 'Legazpi']);

        $this->assertSame('legaspi@ideaserv.com.ph', $branch->notificationEmail());
    }
}
