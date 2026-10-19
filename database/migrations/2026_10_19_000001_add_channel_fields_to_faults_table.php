<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Track where a fault came from (whatsapp, ussd, web, mobile, call_centre, internal ...)
 * and the caller's own reference so retries from a channel never create duplicates.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faults', function (Blueprint $table) {
            if (!Schema::hasColumn('faults', 'channel')) {
                // Existing faults were captured by staff inside Impaza.
                $table->string('channel', 30)->default('internal')->after('fault_ref_number');
                $table->index('channel');
            }
            if (!Schema::hasColumn('faults', 'channel_reference')) {
                // e.g. WhatsApp session/message id, USSD session id, mobile request UUID.
                $table->string('channel_reference', 120)->nullable()->after('channel');
                $table->unique(['channel', 'channel_reference'], 'faults_channel_reference_unique');
            }
            if (!Schema::hasColumn('faults', 'channel_meta')) {
                // Free-form context from the channel (category chosen, bot session, agent name, app version ...)
                $table->json('channel_meta')->nullable()->after('channel_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('faults', function (Blueprint $table) {
            if (Schema::hasColumn('faults', 'channel_reference')) {
                $table->dropUnique('faults_channel_reference_unique');
            }
            if (Schema::hasColumn('faults', 'channel')) {
                $table->dropIndex(['channel']);
            }
        });

        Schema::table('faults', function (Blueprint $table) {
            foreach (['channel_meta', 'channel_reference', 'channel'] as $col) {
                if (Schema::hasColumn('faults', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
