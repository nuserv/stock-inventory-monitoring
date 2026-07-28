<?php

namespace Tests\Unit;

use App\Http\Controllers\MailController;
use App\Jobs\SendBranchAnnouncementBatch;
use PHPUnit\Framework\TestCase;

class SendBranchAnnouncementBatchTest extends TestCase
{
    public function testAnnouncementJobsUseTheDedicatedDatabaseQueue()
    {
        $job = new SendBranchAnnouncementBatch(123);

        $this->assertSame('database', $job->connection);
        $this->assertSame('branch-announcements', $job->queue);
        $this->assertSame(3, $job->tries);
        $this->assertSame(180, $job->timeout);
        $this->assertSame(300, $job->retryAfter);
        $this->assertSame(1, MailController::BRANCH_ANNOUNCEMENT_BATCH_SIZE);
        $this->assertSame(120, MailController::BRANCH_ANNOUNCEMENT_BATCH_DELAY_SECONDS);
    }
}
