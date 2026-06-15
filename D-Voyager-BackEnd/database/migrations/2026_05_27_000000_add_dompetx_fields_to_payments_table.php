<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('gateway')->nullable()->after('payment_method');
            $table->string('gateway_transaction_id')->nullable()->after('gateway');
            $table->string('payment_url')->nullable()->after('gateway_transaction_id');
            $table->json('gateway_response')->nullable()->after('payment_url');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'gateway',
                'gateway_transaction_id',
                'payment_url',
                'gateway_response',
            ]);
        });
    }
};
