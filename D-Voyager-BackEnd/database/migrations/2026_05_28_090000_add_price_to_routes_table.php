<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('routes', 'price')) {
            Schema::table('routes', function (Blueprint $table) {
                $table->decimal('price', 10, 2)->nullable()->after('distance_km');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('routes', 'price')) {
            Schema::table('routes', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        }
    }
};
