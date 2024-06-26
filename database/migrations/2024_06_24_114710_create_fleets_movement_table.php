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
        Schema::create('fleets_movements', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('location');
            $table->enum('type', ['MAINTENANCE', 'UNDER_REPAIR'])->default('MAINTENANCE');
            $table->string('responsible');
            $table->integer('mileage');
            $table->string('service');
            $table->string('image')->nullable();
            $table->string('comments')->nullable();

            $table->foreignId('fleet_id')->references('id')->on('fleets')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fleet_movement');
    }
};
