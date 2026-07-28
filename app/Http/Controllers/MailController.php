<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Mail;
use App\Branch;
use App\BranchEmailAnnouncement;
use App\BranchEmailAnnouncementBatch;
use App\Jobs\SendBranchAnnouncementBatch;
use App\StockRequest;
use Illuminate\Support\Facades\DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MailController extends Controller {
   const BRANCH_ANNOUNCEMENT_CAMPAIGN = 'branch-dr-ddr-digital-copy-announcement-v1';
   const BRANCH_ANNOUNCEMENT_BATCH_SIZE = 1;
   const BRANCH_ANNOUNCEMENT_BATCH_DELAY_SECONDS = 300;

   private function branchAnnouncementRecipients()
   {
      return Branch::query()
         ->whereNotIn('branch', ['Warehouse', 'Main-Office', 'Ups Team'])
         ->where('branch', 'not like', 'Test%')
         ->orderBy('branch')
         ->get()
         ->map(function ($branch) {
            return [
               'branch' => $branch->branch,
               'email' => $branch->notificationEmail(),
            ];
         })
         ->unique('email')
         ->values();
   }

   public function branchAnnouncement()
   {
      $recipients = $this->branchAnnouncementRecipients();
      $announcement = BranchEmailAnnouncement::where(
         'campaign_key',
         self::BRANCH_ANNOUNCEMENT_CAMPAIGN
      )->first();

      return view('branch-email-announcement', compact('recipients', 'announcement'));
   }

   public function sendBranchAnnouncement(Request $request)
   {
      if (config('email.disabled')) {
         return response()->json([
            'message' => 'Email sending is currently disabled.',
         ], 503);
      }

      $recipients = $this->branchAnnouncementRecipients();
      $bcc = $recipients->pluck('email')->all();

      if (empty($bcc)) {
         return response()->json([
            'message' => 'No branch email recipients were found.',
         ], 422);
      }

      $announcement = BranchEmailAnnouncement::where(
         'campaign_key',
         self::BRANCH_ANNOUNCEMENT_CAMPAIGN
      )->first();
      $resend = $request->boolean('resend');

      if ($announcement && in_array($announcement->status, ['queued', 'sending'])) {
         return response()->json($this->announcementStatusPayload($announcement), 202);
      }

      if ($announcement && $announcement->status === 'sent' && !$resend) {
         return response()->json($this->announcementStatusPayload($announcement));
      }

      $batchToDispatchId = null;

      DB::transaction(function () use (
         &$announcement,
         &$batchToDispatchId,
         $bcc,
         $resend
      ) {
         $announcement = BranchEmailAnnouncement::where(
            'campaign_key',
            self::BRANCH_ANNOUNCEMENT_CAMPAIGN
         )->lockForUpdate()->first();

         if (!$announcement) {
            $chunks = array_chunk($bcc, self::BRANCH_ANNOUNCEMENT_BATCH_SIZE);
            $announcement = BranchEmailAnnouncement::create([
               'campaign_key' => self::BRANCH_ANNOUNCEMENT_CAMPAIGN,
               'status' => 'queued',
               'total_recipients' => count($bcc),
               'total_batches' => count($chunks),
            ]);

            $batchToDispatchId = $this->createAnnouncementBatches(
               $announcement,
               $chunks
            );
            return;
         }

         if (in_array($announcement->status, ['queued', 'sending'])) {
            return;
         }

         if ($announcement->status === 'sent') {
            if (!$resend) {
               return;
            }

            $chunks = array_chunk($bcc, self::BRANCH_ANNOUNCEMENT_BATCH_SIZE);
            $announcement->batches()->delete();
            $announcement->update([
               'status' => 'queued',
               'total_recipients' => count($bcc),
               'total_batches' => count($chunks),
               'completed_batches' => 0,
               'failed_batches' => 0,
               'sent_at' => null,
            ]);
            $batchToDispatchId = $this->createAnnouncementBatches(
               $announcement,
               $chunks
            );
            return;
         }

         $failedBatches = $announcement->batches()
            ->where('status', 'failed')
            ->get();
         $failedRecipients = $failedBatches
            ->pluck('recipients')
            ->flatten()
            ->values()
            ->all();
         $nextBatchNumber = $announcement->batches()->max('batch_number') + 1;

         $announcement->batches()
            ->where('status', 'failed')
            ->delete();

         $announcement->update([
            'status' => 'queued',
            'failed_batches' => 0,
         ]);

         $retryChunks = array_chunk(
            $failedRecipients,
            self::BRANCH_ANNOUNCEMENT_BATCH_SIZE
         );

         foreach ($retryChunks as $index => $recipients) {
            $batch = BranchEmailAnnouncementBatch::create([
               'announcement_id' => $announcement->id,
               'batch_number' => $nextBatchNumber + $index,
               'recipients' => $recipients,
               'recipient_count' => count($recipients),
               'status' => 'queued',
            ]);

            if ($batchToDispatchId === null) {
               $batchToDispatchId = $batch->id;
            }
         }

         $announcement->update([
            'total_batches' => $announcement->batches()->count(),
         ]);
      });

      if ($batchToDispatchId !== null) {
         dispatch(new SendBranchAnnouncementBatch($batchToDispatchId));
      }

      $announcement = $announcement->fresh();
      $responseStatus = $announcement->status === 'sent' ? 200 : 202;

      return response()->json(
         $this->announcementStatusPayload($announcement),
         $responseStatus
      );
   }

   public function branchAnnouncementStatus()
   {
      $announcement = BranchEmailAnnouncement::where(
         'campaign_key',
         self::BRANCH_ANNOUNCEMENT_CAMPAIGN
      )->first();

      if (!$announcement) {
         return response()->json([
            'status' => 'ready',
            'message' => 'Announcement is ready to send.',
         ]);
      }

      return response()->json($this->announcementStatusPayload($announcement));
   }

   private function announcementStatusPayload(BranchEmailAnnouncement $announcement)
   {
      $messages = [
         'queued' => 'Announcement queued for background sending.',
         'sending' => 'Announcement is being sent in the background.',
         'sent' => 'Announcement sent to '.$announcement->total_recipients.' branch email addresses.',
         'failed' => $announcement->failed_batches.' email batch(es) failed. You can retry the failed batches.',
      ];

      return [
         'status' => $announcement->status,
         'message' => $messages[$announcement->status],
         'completed_batches' => $announcement->completed_batches,
         'total_batches' => $announcement->total_batches,
         'failed_batches' => $announcement->failed_batches,
      ];
   }

   private function createAnnouncementBatches(
      BranchEmailAnnouncement $announcement,
      array $chunks
   ) {
      $firstBatchId = null;

      foreach ($chunks as $index => $recipients) {
         $batch = BranchEmailAnnouncementBatch::create([
            'announcement_id' => $announcement->id,
            'batch_number' => $index + 1,
            'recipients' => $recipients,
            'recipient_count' => count($recipients),
            'status' => 'queued',
         ]);

         if ($firstBatchId === null) {
            $firstBatchId = $batch->id;
         }
      }

      return $firstBatchId;
   }

   public function basic_email() {
      $name = array('name'=>"Virat Gandhi");
      /*Mail::send(['text'=>'mail'], $name, function($message) {
         $message->to('jerome.lopez.ge2018@gmail.com', 'Tutorials Point')->subject
            ('Laravel Basic Testing Mail');
         $message->from('emorej046@gmail.com','Virat Gandhi');
      });*/
      echo "Basic Email Sent. Check your inbox.";
   }
   public function html_email() {
      $data = array('name'=>"Virat Gandhi");
      /*Mail::send('mail', $data, function($message) {
         $message->to('abc@gmail.com', 'Tutorials Point')->subject
            ('Laravel HTML Testing Mail');
         $message->from('xyz@gmail.com','Virat Gandhi');
      });*/
      echo "HTML Email Sent. Check your inbox.";
   }
   public function attachment_email() {
      $data = array('name'=>"Virat Gandhi");
      if (!config('email.disabled')) {
         Mail::send('mail', $data, function($message) {
            $message->to('abc@gmail.com', 'Tutorials Point')->subject
               ('Laravel Testing Mail with Attachment');
            $message->attach('C:\laravel-master\laravel\public\uploads\image.png');
            $message->attach('C:\laravel-master\laravel\public\uploads\test.txt');
            $message->from('xyz@gmail.com','Virat Gandhi');
         });
      }
      echo "Email Sent with attachment. Check your inbox.";
   }

   public function delreqapproved(Request $request){
      if ($request->action == 'approved') {
          $del = StockRequest::where('code', $request->code)->update(['del_req'=> 2, 'status'=>'DELETED']);
      }else if ($request->action == 'declined') {
          $del = StockRequest::where('code', $request->code)->update(['del_req'=> 3]);
      }
      return response()->json($del);
  }

  public function delapproval(Request $request){
      $code = StockRequest::where('code', $request->code)->first();
      $action = $request->action;
      if ($code) {
          if ($code->del_req == 1) {
              return view('pages.approval', compact('code', 'action'));
          }else if ($code->del_req == 2) {
              return 'Request to delete already approved';
          }
      }else{
          return 'Not found!';
      }
  }
}
