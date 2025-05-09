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
            $table->dropColumn('payment_type_id');
            $table->dropConstrainedForeignId('payment_type_id');
            $table->bigInteger('payment_type_id')->unsigned()->nullable();
            $table->foreign('payment_type_id')->references('id')->on('payments_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotel_reservations', function (Blueprint $table) {
            //
        });
    }
};
