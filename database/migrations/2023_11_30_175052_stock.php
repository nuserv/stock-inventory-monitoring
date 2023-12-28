<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Stock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable()->index();
            $table->text('description');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->string('causer_type')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index(['subject_id', 'subject_type'], 'subject');
            $table->index(['causer_id', 'causer_type'], 'causer');
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('area');
            $table->timestamps();
        });

        Schema::create('billable', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('items_id')->nullable();
            $table->unsignedInteger('customer_branch_id')->nullable();
            $table->unsignedInteger('stocks_id')->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('area_id')->nullable();
            $table->string('branch');
            $table->string('fsr_brchcode')->nullable();
            $table->string('address');
            $table->string('head');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('bstocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('items_id')->nullable();
            $table->string('itemname')->nullable();
            $table->string('serial')->nullable();
            $table->string('count')->nullable();
            $table->unsignedInteger('customer_branches_id')->nullable();
            $table->unsignedInteger('id_branch')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('buffers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('items_id')->nullable();
            $table->string('buffers_no')->nullable();
            $table->integer('qty')->nullable();
            $table->integer('pending')->nullable()->default(0);
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('buffers_no', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('status')->nullable();
            $table->string('buffers_no')->nullable();
            $table->timestamps();
        });

        Schema::create('buffersend', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('items_id')->nullable();
            $table->string('buffers_no')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('category');
            $table->timestamps();
        });

        Schema::create('codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
        });

        Schema::create('conversion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('drno')->nullable();
            $table->string('pullout_date')->nullable();
            $table->integer('branch_id')->nullable();
            $table->unsignedInteger('customer_branches_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('conversion_pos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('bid')->nullable();
            $table->unsignedInteger('customer_branches_id')->nullable();
            $table->string('serial')->nullable();
            $table->string('pos_model')->nullable();
            $table->string('drno')->nullable();
            $table->string('pullout_date')->nullable();
            $table->timestamps();
        });

        Schema::create('customer', function (Blueprint $table) {
            $table->string('custcode', 10)->unique('custcode');
            $table->string('name', 30)->default('')->index('name');
            $table->string('address', 50)->default('');
            $table->string('contact_number', 50);
        });

        Schema::create('customer_branch', function (Blueprint $table) {
            $table->string('custcode', 10);
            $table->string('brchcode', 10)->default('');
            $table->string('brchname', 50)->default('');
            $table->string('addr1', 120)->default('');
            $table->string('addr2', 120)->default('');
            $table->string('contact_person', 20)->default('');
            $table->string('contact_number', 50)->default('');

            $table->index(['custcode', 'brchname'], 'name');
            $table->unique(['custcode', 'brchcode'], 'code');
        });

        Schema::create('customer_branch1', function (Blueprint $table) {
            $table->string('custcode', 10);
            $table->string('brchcode', 10)->default('');
            $table->string('brchname', 50)->default('');
            $table->string('addr1', 120)->default('');
            $table->string('addr2', 120)->default('');
            $table->string('contact_person', 20)->default('');
            $table->string('contact_number', 50)->default('');

            $table->index(['custcode', 'brchname'], 'name');
            $table->unique(['custcode', 'brchcode'], 'code');
        });

        Schema::create('customer_branches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('customer_id')->nullable();
            $table->string('code');
            $table->string('customer_branch');
            $table->string('address')->nullable()->default('');
            $table->string('contact')->nullable()->default('');
            $table->string('cperson')->nullable()->default('');
            $table->string('position')->nullable()->default('');
            $table->string('email_address')->nullable()->default('');
            $table->string('tin_number')->nullable()->default('');
            $table->string('status')->nullable()->default('1');
            $table->timestamps();
        });

        Schema::create('customer_branches_copy', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('customer_id')->nullable();
            $table->string('code');
            $table->string('customer_branch');
            $table->string('address')->nullable();
            $table->string('contact')->nullable();
            $table->string('status')->nullable()->default('1');
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');
            $table->string('customer');
            $table->timestamps();
        });

        Schema::create('defectives', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('branch_id')->index('branch');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id')->nullable()->index('category');
            $table->unsignedBigInteger('items_id')->index('items');
            $table->unsignedInteger('customer_branches_id')->nullable();
            $table->unsignedInteger('bid')->nullable();
            $table->string('serial')->nullable();
            $table->string('status')->nullable();
            $table->string('remarks')->nullable();
            $table->string('return_no')->nullable();
            $table->string('repaired_no')->nullable();
            $table->string('drno')->nullable();
            $table->string('pullout_date')->nullable();
            $table->timestamps();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->integer('user_id')->unique('user_id');
            $table->string('name', 50)->default('');
            $table->string('district', 50)->default('');
        });

        Schema::create('fsr', function (Blueprint $table) {
            $table->string('fsr_num', 10)->default('0')->unique('fsr');
            $table->string('custcode', 10);
            $table->string('custbrch', 10);
            $table->string('contact_person', 50)->default('');
            $table->date('txndate');
            $table->string('address', 120);
            $table->time('txnarr');
            $table->time('txnstart');
            $table->time('txnend');
            $table->char('typeofwork', 1)->default('S');
            $table->char('partsreplace', 1)->nullable()->default('');
            $table->string('pdf_file', 30);
            $table->string('user_id', 5);
            $table->string('brchcode', 5);
            $table->string('distcode', 5);
            $table->string('liquidated', 2)->default('N');
            $table->timestamps();

            $table->index(['txndate', 'custcode', 'custbrch'], 'fsrinfo');
        });

        Schema::create('iclock', function (Blueprint $table) {
            $table->longText('id')->nullable();
            $table->timestamps();
        });

        Schema::create('initials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('items_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->integer('qty')->nullable();
            $table->timestamps();
        });

        Schema::create('initials_copy', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('items_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->integer('qty')->nullable();
            $table->timestamps();
        });

        Schema::create('ipaddress', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ipaddress', 50)->nullable()->default('0');
            $table->timestamp('stock')->nullable();
            $table->timestamp('ticket')->nullable();
            $table->timestamp('repository')->nullable();
            $table->timestamp('hrms')->nullable();
            $table->timestamps();
        });

        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('item');
            $table->string('category_id')->nullable();
            $table->string('specs', 2000)->nullable()->default('');
            $table->string('UOM')->nullable();
            $table->string('n_a')->nullable();
            $table->string('prodcode')->default('[BLANK]');
            $table->string('minimum')->default('1');
            $table->string('assemble')->default('NO');
            $table->string('serialize')->default('NO');
            $table->string('deleted')->default('NO');
            $table->timestamps();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('loans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('approved_by')->nullable();
            $table->string('request_no')->nullable();
            $table->unsignedInteger('from_branch_id')->nullable();
            $table->unsignedInteger('to_branch_id')->nullable();
            $table->unsignedInteger('items_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('log_activity', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subject');
            $table->string('before');
            $table->string('after');
            $table->string('url');
            $table->string('method');
            $table->string('ip');
            $table->string('agent')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->primary(['permission_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->primary(['role_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
        });

        Schema::create('msg', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('msg')->nullable();
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create('pm_branches', function (Blueprint $table) {
            $table->string('customer_branches_code')->unique('customer_branches_code');
            $table->string('Conversion')->nullable();
            $table->bigInteger('branch_id')->nullable();
            $table->integer('quarter')->nullable();
        });

        Schema::create('pm_sched', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index('FK_pm_sched_users');
            $table->string('customer_id')->nullable();
            $table->string('schedule')->nullable();
            $table->string('fsrno')->nullable();
            $table->char('typeofwork', 1)->nullable();
            $table->string('Status')->nullable()->default('Scheduled');
            $table->timestamps();
        });

        Schema::create('pms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('stocks_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('items_id')->nullable();
            $table->string('customer_ids')->nullable();
            $table->string('serial')->nullable();
            $table->timestamps();
        });

        Schema::create('prepared_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('branch_id')->nullable()->index('FK_prepared_items_branches');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('intransit')->nullable();
            $table->unsignedInteger('items_id')->nullable();
            $table->string('request_no')->nullable();
            $table->string('serial')->nullable();
            $table->string('schedule')->nullable();
            $table->timestamps();
        });

        Schema::create('pullouts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('items_id')->nullable();
            $table->string('serial')->nullable();
            $table->string('status')->nullable();
            $table->string('pullout_no')->nullable();
            $table->timestamps();
        });

        Schema::create('pullouts_no', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
            $table->string('status')->nullable();
            $table->string('pullout_no')->nullable();
            $table->timestamps();
        });

        Schema::create('repaired_no', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('status')->nullable();
            $table->string('repaired_no')->nullable()->unique('repaired_no');
            $table->timestamps();
        });

        Schema::create('requested_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('request_no')->nullable();
            $table->unsignedBigInteger('items_id')->index('FK_requested_items_items');
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->unsignedBigInteger('pending')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('request_no')->nullable();
            $table->longText('code')->nullable();
            $table->string('status', 50)->nullable();
            $table->string('stat', 50)->nullable();
            $table->string('del_req', 50)->nullable()->default('0');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('resolved_by')->nullable();
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('customer_branch_id')->nullable();
            $table->string('ticket', 50)->nullable();
            $table->integer('branch_id')->nullable();
            $table->string('type', 50)->nullable();
            $table->string('remarks', 999)->nullable();
            $table->string('area_id')->nullable();
            $table->string('schedule', 50)->nullable();
            $table->string('schedby', 50)->nullable();
            $table->timestamp('intransit')->nullable();
            $table->string('intransitval', 50)->nullable();
            $table->timestamps();
            $table->unsignedInteger('pending')->nullable();
        });

        Schema::create('responders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('return_to_mail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('return_no')->nullable();
            $table->timestamps();
        });

        Schema::create('returns_no', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
            $table->string('status')->nullable();
            $table->string('return_no')->nullable();
            $table->timestamps();
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id')->index('role_has_permissions_role_id_foreign');

            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create('service_outs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('items_id')->nullable();
            $table->unsignedInteger('stocks_id')->nullable();
            $table->string('status', 50)->nullable();
            $table->unsignedInteger('customer_branch_id')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_request_code', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('code')->nullable();
            $table->integer('branch_id')->nullable();
            $table->timestamps();
        });

        Schema::create('stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index('usr');
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedBigInteger('branch_id')->index('branches');
            $table->unsignedBigInteger('items_id')->index('item');
            $table->string('itemname')->nullable();
            $table->string('serial')->nullable();
            $table->string('status')->nullable();
            $table->unsignedInteger('customer_branches_id')->nullable();
            $table->unsignedInteger('warranty')->nullable();
            $table->unsignedInteger('id_branch')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('user_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('activity');
            $table->string('ipaddress')->nullable();
            $table->string('fullname')->nullable();
            $table->string('branch')->nullable();
            $table->string('service')->nullable();
            $table->string('company')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedInteger('area_id');
            $table->unsignedInteger('branch_id');
            $table->string('middlename')->nullable();
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('verifyToken');
            $table->integer('status');
            $table->tinyInteger('verified')->nullable()->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->timestamp('email_verified_at')->nullable();
        });

        Schema::create('verify_users', function (Blueprint $table) {
            $table->integer('user_id');
            $table->string('token');
            $table->timestamps();
        });

        Schema::create('warehouse_initial', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('items_id')->nullable();
            $table->integer('qty')->nullable();
            $table->timestamps();
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('request_no')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedBigInteger('items_id');
            $table->string('serial')->nullable();
            $table->string('status')->nullable();
            $table->string('branch_id')->nullable();
            $table->string('schedule', 50)->nullable();
            $table->timestamps();
        });

        Schema::table('defectives', function (Blueprint $table) {
            $table->foreign(['category_id'], 'category')->references(['id'])->on('categories');
            $table->foreign(['branch_id'], 'branch')->references(['id'])->on('branches');
            $table->foreign(['items_id'], 'items')->references(['id'])->on('items');
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->foreign(['permission_id'])->references(['id'])->on('permissions')->onDelete('CASCADE');
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->foreign(['role_id'])->references(['id'])->on('roles')->onDelete('CASCADE');
        });

        Schema::table('pm_sched', function (Blueprint $table) {
            $table->foreign(['user_id'], 'FK_pm_sched_users')->references(['id'])->on('users');
        });

        Schema::table('prepared_items', function (Blueprint $table) {
            $table->foreign(['branch_id'], 'FK_prepared_items_branches')->references(['id'])->on('branches');
        });

        Schema::table('requested_items', function (Blueprint $table) {
            $table->foreign(['items_id'], 'FK_requested_items_items')->references(['id'])->on('items');
        });

        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->foreign(['role_id'])->references(['id'])->on('roles')->onDelete('CASCADE');
            $table->foreign(['permission_id'])->references(['id'])->on('permissions')->onDelete('CASCADE');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->foreign(['items_id'], 'item')->references(['id'])->on('items');
            $table->foreign(['branch_id'], 'branches')->references(['id'])->on('branches');
            $table->foreign(['user_id'], 'usr')->references(['id'])->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
