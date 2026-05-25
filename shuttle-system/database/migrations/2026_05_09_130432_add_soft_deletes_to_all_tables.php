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
        Schema::table('users', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('locations', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('vehicles', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('routes', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('schedules', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('drivers', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('customers', function (Blueprint $table) { $table->softDeletes(); });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('locations', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('vehicles', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('routes', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('schedules', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('drivers', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('customers', function (Blueprint $table) { $table->dropSoftDeletes(); });
    }
};
