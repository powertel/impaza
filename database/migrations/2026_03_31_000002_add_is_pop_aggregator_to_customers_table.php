<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'is_pop_aggregator')) {
                $table->boolean('is_pop_aggregator')->default(false)->after('contact_number');
                $table->index('is_pop_aggregator');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'is_pop_aggregator')) {
                $table->dropIndex(['is_pop_aggregator']);
                $table->dropColumn('is_pop_aggregator');
            }
        });
    }
};

