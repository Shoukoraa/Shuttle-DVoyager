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
        Schema::table('vehicles', function (Blueprint $table) {
            // Add driver_id as unique FK (1 driver = 1 vehicle)
            $table->foreignId('driver_id')->nullable()->unique()->constrained()->cascadeOnDelete();
            
            // Add timestamps for better tracking
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['driver_id']);
            $table->dropColumn(['driver_id', 'created_at', 'updated_at']);
        });
    }
};
