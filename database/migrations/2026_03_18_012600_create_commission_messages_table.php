<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('commission_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_request_id')->constrained('commission_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('kind')->default('message');
            $table->text('message');
            $table->timestamps();

            $table->index(['commission_request_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_messages');
    }
};
