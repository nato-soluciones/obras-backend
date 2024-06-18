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
        Schema::table('additionals_categories', function (Blueprint $table) {
            $table->dropForeign(['additional_id']);
            $table->foreign('additional_id')->references('id')->on('additionals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('additionals_categories', function (Blueprint $table) {
            $table->dropForeign(['additional_id']);

            // Restaurar la restricción de clave foránea incorrecta
            $table->foreign('additional_id')->references('id')->on('budgets')->onDelete('cascade');
        });
    }
};
