<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Chatbot Categories (e.g., Akun & Keamanan)
        Schema::create('chatbot_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable(); // Ionic icon name
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. Chatbot Problems & Solutions (e.g., Lupa Password)
        Schema::create('chatbot_problems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('chatbot_categories')->cascadeOnDelete();
            $table->string('title');
            $table->text('solution_text');
            $table->text('additional_solution')->nullable(); // Alternative steps if first fails
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Conversation Sessions
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['bot', 'active', 'resolved'])->default('bot');
            $table->foreignId('last_category_id')->nullable()->constrained('chatbot_categories');
            $table->foreignId('last_problem_id')->nullable()->constrained('chatbot_problems');
            $table->timestamps();
        });

        // 4. Message Log
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('chat_session_id')->constrained('chat_sessions')->cascadeOnDelete();
            $table->enum('sender_type', ['bot', 'user', 'admin']);
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('message_type', ['text', 'options', 'solution', 'feedback_request']);
            $table->text('message_content');
            $table->json('payload')->nullable(); // Stores button options/JSON metadata
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
        Schema::dropIfExists('chatbot_problems');
        Schema::dropIfExists('chatbot_categories');
    }
};
