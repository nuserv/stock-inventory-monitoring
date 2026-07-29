<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRotationPositionToMailPoolAccountsTable extends Migration
{
    public function up()
    {
        Schema::table('mail_pool_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('rotation_position')
                ->default(0)
                ->after('slot')
                ->index();
        });
    }

    public function down()
    {
        Schema::table('mail_pool_accounts', function (Blueprint $table) {
            $table->dropIndex(['rotation_position']);
            $table->dropColumn('rotation_position');
        });
    }
}
