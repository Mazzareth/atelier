<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('commission_requests', function (Blueprint $table) {
            $table->string('tracker_stage')->nullable()->after('status');
            $table->timestamp('tracker_stage_updated_at')->nullable()->after('responded_at');

            $table->index(['artist_id', 'tracker_stage']);
        });
    }

    public function down(): void
    {
        Schema::table('commission_requests', function (Blueprint $table) {
            $table->dropIndex(['artist_id', 'tracker_stage']);
            $table->dropColumn(['tracker_stage', 'tracker_stage_updated_at']);
        });
    }
};
