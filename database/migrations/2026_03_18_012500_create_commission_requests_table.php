<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('commission_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('details');
            $table->decimal('budget', 10, 2)->nullable();
            $table->string('status')->default('pending');
            $table->text('artist_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['artist_id', 'status']);
            $table->index(['requester_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_requests');
    }
};
