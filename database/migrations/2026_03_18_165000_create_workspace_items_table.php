<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('workspace_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_request_id')->constrained('commission_requests')->cascadeOnDelete();
            $table->string('type'); // image | note
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('x')->default(40);
            $table->integer('y')->default(40);
            $table->integer('width')->default(260);
            $table->integer('height')->default(180);
            $table->integer('z_index')->default(1);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_items');
    }
};
