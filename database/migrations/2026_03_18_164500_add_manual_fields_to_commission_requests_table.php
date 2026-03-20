<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('commission_requests', function (Blueprint $table) {
            $table->string('client_name')->nullable()->after('requester_id');
            $table->string('client_contact')->nullable()->after('client_name');
            $table->boolean('is_manual')->default(false)->after('client_contact');
        });
    }

    public function down(): void
    {
        Schema::table('commission_requests', function (Blueprint $table) {
            $table->dropColumn(['client_name', 'client_contact', 'is_manual']);
        });
    }
};
