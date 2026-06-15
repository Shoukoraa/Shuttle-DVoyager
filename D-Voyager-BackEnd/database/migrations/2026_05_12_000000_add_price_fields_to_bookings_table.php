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
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('price_per_seat', 10, 2)->nullable()->after('total_seat');
            $table->decimal('total_price', 10, 2)->nullable()->after('price_per_seat');
            $table->decimal('service_fee', 10, 2)->default(0)->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['price_per_seat', 'total_price', 'service_fee']);
        });
    }
};
