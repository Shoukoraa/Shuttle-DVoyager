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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->index();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['percentage', 'flat']);
            $table->decimal('value', 10, 2);
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->decimal('min_transaction', 10, 2)->default(0);
            $table->dateTime('expiry_date');
            $table->boolean('is_new_user_only')->default(false);
            $table->string('badge_text')->nullable();
            $table->string('icon')->default('gift-outline');
            $table->string('theme_color')->default('#FFC107');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
