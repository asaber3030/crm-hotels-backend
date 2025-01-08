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
        Schema::disableForeignKeyConstraints();

        Schema::create('hotel_reservations', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('reservation_id')->unsigned();
            $table->foreign('reservation_id')->references('id')->on('reservations');

            $table->bigInteger('hotel_id')->unsigned();
            $table->foreign('hotel_id')->references('id')->on('hotels');

            $table->bigInteger('city_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('cities');

            $table->bigInteger('meal_id')->unsigned();
            $table->foreign('meal_id')->references('id')->on('meals');

            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies');

            $table->bigInteger('rate_id')->unsigned();
            $table->foreign('rate_id')->references('id')->on('rates');

            $table->date('check_in');
            $table->date('check_out');
            $table->integer('rooms_count');
            $table->string('view');
            $table->integer('pax_count');
            $table->integer('adults');
            $table->integer('children');
            $table->date('option_date');
            $table->string('confirmation_number');
            $table->string('price');
            $table->enum('status', ['in_revision', 'confirmed', 'refunded', 'cancelled', 'guaranteed']);

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_reservation');
    }
};
