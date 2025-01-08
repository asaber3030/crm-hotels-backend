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

        Schema::create('airport_reservations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('reservation_id')->unsigned();
            $table->foreign('reservation_id')->references('id')->on('reservations');
            $table->string('airport_name');
            $table->string('airline');
            $table->string('runner');
            $table->integer('price');
            $table->string('flight_number');
            $table->string('coming_from');
            $table->string('passenger_count');
            $table->enum('status', ['pending', 'done', 'cancelled']);
            $table->date('arrival_date');
            $table->time('arrival_time');
            $table->integer('persons_count');
            $table->integer('statment');
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
        Schema::dropIfExists('airport_reservation');
    }
};
