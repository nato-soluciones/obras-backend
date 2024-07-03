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
        Schema::create('unit_conversions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_unit_id');
            $table->unsignedBigInteger('to_unit_id');
            $table->decimal('conversion_factor', 20, 10);
            $table->timestamps();

            $table->foreign('from_unit_id')->references('id')->on('measurement_units')->onDelete('cascade');
            $table->foreign('to_unit_id')->references('id')->on('measurement_units')->onDelete('cascade');

            $table->unique(['from_unit_id', 'to_unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_conversions');
    }
};
