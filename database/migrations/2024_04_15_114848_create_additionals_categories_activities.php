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
        Schema::create('additionals_categories_activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('provider_id')->unsigned()->nullable();
            $table->string('unit',50);
            $table->decimal('unit_cost', 18, 2)->nullable();
            $table->decimal('profit', 18, 2)->nullable();
            $table->decimal('unit_price', 18, 2);
            $table->decimal('quantity', 18, 2);
            $table->decimal('subtotal', 18, 2);

            $table->foreignId('additional_category_id')->references('id')->on('additionals_categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additionals_categories_activities');
    }
};
