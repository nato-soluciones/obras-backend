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
        Schema::create('measurement_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('abbreviation', 10);
            $table->decimal('min_fraction', 8, 3);
            $table->string('group', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('measurement_unit_id');
            $table->timestamps();

            $table->foreign('measurement_unit_id')->references('id')->on('measurement_units')->onDelete('restrict');
        });        

        Schema::create('obra_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('obra_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('quantity', 12, 2);
            $table->timestamps();

            $table->foreign('obra_id')->references('id')->on('obras')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('restrict');
        });

        Schema::create('obra_material_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('obra_material_id');
            $table->date('date');
            $table->enum('movement_type', ['ACOPIO', 'RETIRO']);
            $table->unsignedBigInteger('measurement_unit_id');
            $table->decimal('quantity', 10, 2);
            $table->string('description')->nullable();
            $table->text('observation')->nullable();
            $table->timestamps();

            $table->foreign('obra_material_id')->references('id')->on('obra_materials')->onDelete('cascade');
            $table->foreign('measurement_unit_id')->references('id')->on('measurement_units')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obra_material_movements');
        Schema::dropIfExists('obra_materials');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('measurement_units');
    }
};
