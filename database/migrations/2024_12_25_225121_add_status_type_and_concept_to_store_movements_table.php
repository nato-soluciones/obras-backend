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
            $table->foreignId('store_movement_type_id')->constrained('store_movement_types')->onDelete('set null');
            $table->foreignId('store_movement_status_id')->constrained('store_movement_statuses')->onDelete('set null');
            $table->foreignId('store_movement_concept_id')->constrained('store_movement_concepts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_movements', function (Blueprint $table) {
            $table->dropForeign(['store_movement_type_id']);
            $table->dropColumn('store_movement_type_id');
            $table->dropForeign(['store_movement_status_id']);
            $table->dropColumn('store_movement_status_id');
            $table->dropForeign(['store_movement_concept_id']);
            $table->dropColumn('store_movement_concept_id');
        });
    }
};
