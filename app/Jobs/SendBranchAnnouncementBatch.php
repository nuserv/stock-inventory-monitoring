<?php

namespace App\Jobs;

use App\BranchEmailAnnouncement;
use App\BranchEmailAnnouncementBatch;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class SendBranchAnnouncementBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 180;

    public $retryAfter = 300;

    private $batchId;

    public function __construct($batchId)
    {
        $this->batchId = $batchId;
        $this->onConnection('database')
            ->onQueue('branch-announcements');
    }

    public function handle()
    {
        $batch = BranchEmailAnnouncementBatch::findOrFail($this->batchId);

        if ($batch->status === 'sent') {
            return;
        }

        $batch->update([
            'status' => 'sending',
            'attempts' => $batch->attempts + 1,
            'last_error' => null,
        ]);

        BranchEmailAnnouncement::where('id', $batch->announcement_id)
            ->where('status', 'queued')
            ->update(['status' => 'sending']);

        $recipients = $batch->recipients;

        Mail::send('emails.branch-dr-ddr-announcement', [], function ($message) use ($recipients) {
            $message->to('jolopez@ideaserv.com.ph', 'Jerome Lopez')
                ->bcc($recipients)
                ->subject('DR and DDR Digital Copy Email Announcement');
        });

        if (count(Mail::failures()) > 0) {
            throw new RuntimeException('The email server rejected one or more recipients.');
        }

        DB::transaction(function () {
            $batch = BranchEmailAnnouncementBatch::lockForUpdate()->findOrFail($this->batchId);

            if ($batch->status === 'sent') {
                return;
            }

            $batch->update([
                'status' => 'sent',
                'sent_at' => now(),
                'last_error' => null,
            ]);

            $announcement = BranchEmailAnnouncement::lockForUpdate()
                ->findOrFail($batch->announcement_id);
            $announcement->completed_batches++;

            if ($announcement->completed_batches >= $announcement->total_batches) {
                $announcement->status = 'sent';
                $announcement->sent_at = now();
            }

            $announcement->save();
        });
    }

    public function failed(Exception $exception)
    {
        $message = mb_substr($exception->getMessage(), 0, 1000);

        DB::transaction(function () use ($message) {
            $batch = BranchEmailAnnouncementBatch::lockForUpdate()->find($this->batchId);

            if (!$batch || $batch->status === 'sent' || $batch->status === 'failed') {
                return;
            }

            $batch->update([
                'status' => 'failed',
                'last_error' => $message,
            ]);

            $announcement = BranchEmailAnnouncement::lockForUpdate()
                ->find($batch->announcement_id);

            if ($announcement) {
                $announcement->status = 'failed';
                $announcement->failed_batches++;
                $announcement->save();
            }
        });
    }
}
