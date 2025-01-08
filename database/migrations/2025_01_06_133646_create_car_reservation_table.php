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

        Schema::create('car_reservations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('reservation_id')->unsigned();
            $table->bigInteger('driver_id')->unsigned();
            $table->foreign('reservation_id')->references('id')->on('reservations');
            $table->foreign('driver_id')->references('id')->on('drivers');
            $table->string('airline');
            $table->string('meeting_point');
            $table->date('arrival_date');
            $table->string('arrival_time');
            $table->string('coming_from');
            $table->string('comments');
            $table->enum('status', ['pending', 'done', 'cancelled']);
            $table->decimal('price');
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
        Schema::dropIfExists('car_reservation');
    }
};
