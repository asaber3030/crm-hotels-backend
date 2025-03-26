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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->bigInteger('rate_id')->unsigned()->nullable()->after('room_id');
            $table->foreign('rate_id')->references('id')->on('rates');
            $table->bigInteger('payment_type_id')->unsigned()->nullable()->after('rate_id');
            $table->foreign('payment_type_id')->references('id')->on('payments_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            //
        });
    }
};
