<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicketToServiceOutsAndBillableTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('service_outs', 'ticket')) {
            Schema::table('service_outs', function (Blueprint $table) {
                $table->string('ticket', 50)->nullable()->after('customer_branch_id');
            });
        }

        if (!Schema::hasColumn('billable', 'ticket')) {
            Schema::table('billable', function (Blueprint $table) {
                $table->string('ticket', 50)->nullable()->after('stocks_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('service_outs', 'ticket')) {
            Schema::table('service_outs', function (Blueprint $table) {
                $table->dropColumn('ticket');
            });
        }

        if (Schema::hasColumn('billable', 'ticket')) {
            Schema::table('billable', function (Blueprint $table) {
                $table->dropColumn('ticket');
            });
        }
    }
}
