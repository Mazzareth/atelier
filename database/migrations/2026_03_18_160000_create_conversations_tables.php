<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_one_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_two_id')->constrained('users')->cascadeOnDelete();
            $table->string('kind')->default('direct'); // direct | commission
            $table->string('title')->nullable();
            $table->timestamp('user_one_last_read_at')->nullable();
            $table->timestamp('user_two_last_read_at')->nullable();
            $table->timestamps();

            $table->index(['user_one_id', 'user_two_id']);
        });

        Schema::create('conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('kind')->default('message'); // message | system
            $table->text('message');
            $table->timestamps();
        });

        Schema::table('commission_requests', function (Blueprint $table) {
            $table->foreignId('conversation_id')->nullable()->after('requester_id')->constrained('conversations')->nullOnDelete();
        });

        $requests = DB::table('commission_requests')->get();

        foreach ($requests as $request) {
            $conversationId = DB::table('conversations')->insertGetId([
                'user_one_id' => $request->artist_id,
                'user_two_id' => $request->requester_id,
                'kind' => 'commission',
                'title' => $request->title,
                'user_one_last_read_at' => $request->artist_last_read_at ?? null,
                'user_two_last_read_at' => $request->requester_last_read_at ?? null,
                'created_at' => $request->created_at ?? now(),
                'updated_at' => $request->updated_at ?? now(),
            ]);

            DB::table('commission_requests')
                ->where('id', $request->id)
                ->update(['conversation_id' => $conversationId]);

            $messages = DB::table('commission_messages')
                ->where('commission_request_id', $request->id)
                ->orderBy('id')
                ->get();

            foreach ($messages as $message) {
                DB::table('conversation_messages')->insert([
                    'conversation_id' => $conversationId,
                    'user_id' => $message->user_id,
                    'kind' => $message->kind ?? 'message',
                    'message' => $message->message,
                    'created_at' => $message->created_at ?? now(),
                    'updated_at' => $message->updated_at ?? now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('commission_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('conversation_id');
        });

        Schema::dropIfExists('conversation_messages');
        Schema::dropIfExists('conversations');
    }
};
