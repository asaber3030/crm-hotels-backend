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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->references('id')->on('agents')->onDelete('cascade');
            $table->foreignId('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
            $table->foreignId('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('creation_date');
            $table->date('from1');
            $table->date('to1');
            $table->string('from2')->nullable();
            $table->string('to2')->nullable();
            $table->string('customer_name');
            $table->string('proxy_name');
            $table->string('reservation_number');
            $table->integer('nights_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
