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
        Schema::create('fleets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand');
            $table->string('model');
            $table->integer('value')->nullable();
            $table->integer('mileage')->nullable();
            $table->integer('initial_mileage')->nullable();
            $table->string('domain')->nullable();
            $table->string('image')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('vtv_expiration')->nullable();
            $table->date('next_plate_payment')->nullable();

            $table->enum('status', ['IN_USE', 'UNDER_REPAIR', 'DAMAGED', 'SOLD'])->default('IN_USE');
            $table->enum('type', ['TRUCK', 'MOTORCYCLE', 'CAR', 'UTILITY', 'OTHER'])->default('OTHER');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fleet');
    }
};
