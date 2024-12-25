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
        Schema::table('store_movement_concepts', function (Blueprint $table) {
            $table->unsignedBigInteger('movement_type_id');
            $table->foreign('movement_type_id')->references('id')->on('store_movement_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_movement_concepts', function (Blueprint $table) {
            $table->dropForeign(['movement_type_id']);
            $table->dropColumn('movement_type_id');
        });
    }
};
