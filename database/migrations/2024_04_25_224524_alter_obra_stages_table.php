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
        Schema::table('obra_stages', function (Blueprint $table) {
            $table->decimal('obra_percentage', 5, 2)->default(0.00)->comment('Porcentaje que representa esta etapa en la obra');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obra_stages', function (Blueprint $table) {
            $table->dropColumn('obra_percentage');
        }); 
    }
};
