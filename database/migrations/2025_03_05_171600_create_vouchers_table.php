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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->references('id')->on('companies')->constrained();
            $table->foreignId('city_id')->references('id')->on('cities')->constrained();
            $table->foreignId('hotel_id')->references('id')->on('hotels')->constrained();
            $table->foreignId('meal_id')->references('id')->on('meals')->constrained();
            $table->foreignId('room_id')->references('id')->on('rooms')->constrained();
            $table->string('view');
            $table->integer('adults');
            $table->integer('children');
            $table->string('internal_confirmation');
            $table->string('nationality');
            $table->string('client_name');
            $table->string('hcn');
            $table->integer('pax');
            $table->integer('rooms_count');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('confirmed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
