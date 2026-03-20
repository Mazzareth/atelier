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
        Schema::table('users', function (Blueprint $row) {
            $row->decimal('total_revenue', 12, 2)->default(0.00)->after('role');
            $row->integer('subscriber_count')->default(0)->after('total_revenue');
            $row->integer('commission_count')->default(0)->after('subscriber_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $row) {
            $row->dropColumn(['total_revenue', 'subscriber_count', 'commission_count']);
        });
    }
};
