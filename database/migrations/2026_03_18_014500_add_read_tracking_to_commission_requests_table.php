<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('commission_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('commission_requests', 'artist_last_read_at')) {
                $table->timestamp('artist_last_read_at')->nullable()->after('responded_at');
            }
            if (! Schema::hasColumn('commission_requests', 'requester_last_read_at')) {
                $table->timestamp('requester_last_read_at')->nullable()->after('artist_last_read_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('commission_requests', function (Blueprint $table) {
            if (Schema::hasColumn('commission_requests', 'artist_last_read_at')) {
                $table->dropColumn('artist_last_read_at');
            }
            if (Schema::hasColumn('commission_requests', 'requester_last_read_at')) {
                $table->dropColumn('requester_last_read_at');
            }
        });
    }
};
