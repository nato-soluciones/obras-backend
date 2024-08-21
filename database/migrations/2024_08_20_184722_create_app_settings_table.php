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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('module'); // Módulo o sección de la aplicación
            $table->string('key'); // Nombre del parámetro
            $table->text('value')->nullable(); // Valor del parámetro
            $table->string('type'); // Tipo de dato del parámetro (ej. int, boolean, etc.)
            $table->string('description')->nullable(); // Descripción del parámetro
            $table->timestamps();

            // Clave única de module y key
            $table->unique(['module', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
