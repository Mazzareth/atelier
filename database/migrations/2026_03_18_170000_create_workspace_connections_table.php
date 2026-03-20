<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('workspace_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_request_id')->constrained('commission_requests')->cascadeOnDelete();
            $table->foreignId('from_workspace_item_id')->constrained('workspace_items')->cascadeOnDelete();
            $table->foreignId('to_workspace_item_id')->constrained('workspace_items')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_connections');
    }
};
