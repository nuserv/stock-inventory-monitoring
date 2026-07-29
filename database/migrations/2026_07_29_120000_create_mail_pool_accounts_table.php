<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailPoolAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('mail_pool_accounts', function (Blueprint $table) {
            $table->unsignedTinyInteger('slot')->primary();
            $table->timestamp('last_used_at')->nullable()->index();
            $table->timestamp('cooldown_until')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mail_pool_accounts');
    }
}
