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
        Schema::create('quality_control_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quality_control_id');
            $table->unsignedBigInteger('template_item_id');
            $table->boolean('passed')->default(false);
            $table->timestamps();

            $table->foreign('quality_control_id')->references('id')->on('quality_controls')->onDelete('cascade');
            $table->foreign('template_item_id')->references('id')->on('quality_control_template_items')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_control_items');
    }
};
