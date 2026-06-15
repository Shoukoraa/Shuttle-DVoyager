<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('reviews', 'booking_id')) {
            return;
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('booking_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unique('booking_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('reviews', 'booking_id')) {
            return;
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique(['booking_id']);
            $table->dropConstrainedForeignId('booking_id');
        });
    }
};
