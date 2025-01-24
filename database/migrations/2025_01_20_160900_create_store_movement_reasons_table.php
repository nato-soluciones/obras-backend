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
        Schema::create('store_movement_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('store_movements', function (Blueprint $table) {
            $table->foreignId('store_movement_reason_id')->nullable()
                  ->constrained('store_movement_reasons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_movements', function (Blueprint $table) {
            $table->dropForeign(['store_movement_reason_id']);
            $table->dropColumn('store_movement_motive_id');
        });
        Schema::dropIfExists('store_movement_reasons');
    }
};
