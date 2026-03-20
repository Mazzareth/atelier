<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('commission_requests', function (Blueprint $table) {
            $table->json('reference_images')->nullable()->after('budget');
        });

        Schema::table('conversation_messages', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('message');
        });

        Schema::table('commission_messages', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('commission_messages', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });

        Schema::table('conversation_messages', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });

        Schema::table('commission_requests', function (Blueprint $table) {
            $table->dropColumn('reference_images');
        });
    }
};
