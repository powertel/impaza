<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'customer_status')) {
                $table->unsignedInteger('customer_status')->default(2)->after('account_manager_id');
            }
        });

        if (!Schema::hasTable('audits')) {
            Schema::create('audits', function (Blueprint $table) {
                $table->increments('id');
                $table->string('entity_type');
                $table->unsignedInteger('entity_id');
                $table->string('action');
                $table->unsignedInteger('user_id')->nullable();
                $table->string('notes')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'customer_status')) {
                $table->dropColumn('customer_status');
            }
        });
        Schema::dropIfExists('audits');
    }
};