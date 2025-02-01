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
        Schema::table('hotel_reservations', function (Blueprint $table) {
            $table->enum('status', ['new', 'in_revision', 'confirmed', 'refunded', 'cancelled', 'guaranteed'])->default('new')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotel_reservations_update', function (Blueprint $table) {
            //
        });
    }
};
