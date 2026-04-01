<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('links', function (Blueprint $table) {
            // Add optional metadata fields for links
            $table->string('jcc_number')->nullable()->after('id');
            $table->string('service_type')->nullable()->after('jcc_number');
            $table->string('capacity')->nullable()->after('service_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn(['jcc_number', 'service_type', 'capacity']);
        });
    }
};