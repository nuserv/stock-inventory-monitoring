<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchEmailAnnouncementQueueTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('branch_email_announcements')) {
            Schema::create('branch_email_announcements', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('campaign_key')->unique();
                $table->string('status', 20)->default('queued');
                $table->unsignedInteger('total_recipients');
                $table->unsignedInteger('total_batches');
                $table->unsignedInteger('completed_batches')->default(0);
                $table->unsignedInteger('failed_batches')->default(0);
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('branch_email_announcement_batches')) {
            Schema::create('branch_email_announcement_batches', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('announcement_id')->index();
                $table->unsignedInteger('batch_number');
                $table->text('recipients');
                $table->unsignedInteger('recipient_count');
                $table->string('status', 20)->default('queued');
                $table->unsignedInteger('attempts')->default(0);
                $table->text('last_error')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();

                $table->unique(
                    ['announcement_id', 'batch_number'],
                    'branch_announcement_batch_unique'
                );
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('branch_email_announcement_batches');
        Schema::dropIfExists('branch_email_announcements');
    }
}
