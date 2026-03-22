<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('viewer_identity', 32)->nullable()->after('active_profile');
            $table->string('theme', 64)->nullable()->after('viewer_identity');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['viewer_identity', 'theme']);
        });
    }
};
