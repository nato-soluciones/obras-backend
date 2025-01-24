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
        Schema::table('store_movements', function (Blueprint $table) {
            $table->dropForeign('movements_material_id_foreign');
            $table->dropColumn(['quantity', 'material_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_movements', function (Blueprint $table) {
            $table->foreignId('material_id')->constrained('materials');
            $table->float('quantity');
        });
    }
};
