<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('links', function (Blueprint $table) {
            if (!Schema::hasColumn('links', 'jcc_number')) {
                $table->string('jcc_number')->nullable()->after('contract_number');
                $table->unique('jcc_number');
            }
            if (!Schema::hasColumn('links', 'sapcodes')) {
                $table->string('sapcodes')->nullable()->after('jcc_number');
            }
            if (!Schema::hasColumn('links', 'comment')) {
                $table->text('comment')->nullable()->after('sapcodes');
            }
            if (!Schema::hasColumn('links', 'quantity')) {
                $table->unsignedInteger('quantity')->nullable()->after('comment');
            }
            if (!Schema::hasColumn('links', 'service_type')) {
                $table->string('service_type')->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('links', 'capacity')) {
                $table->string('capacity')->nullable()->after('service_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            if (Schema::hasColumn('links', 'capacity')) {
                $table->dropColumn('capacity');
            }
            if (Schema::hasColumn('links', 'service_type')) {
                $table->dropColumn('service_type');
            }
            if (Schema::hasColumn('links', 'quantity')) {
                $table->dropColumn('quantity');
            }
            if (Schema::hasColumn('links', 'comment')) {
                $table->dropColumn('comment');
            }
            if (Schema::hasColumn('links', 'sapcodes')) {
                $table->dropColumn('sapcodes');
            }
            if (Schema::hasColumn('links', 'jcc_number')) {
                $table->dropUnique(['jcc_number']);
                $table->dropColumn('jcc_number');
            }
        });
    }
};