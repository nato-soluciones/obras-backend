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
        Schema::create('budgets_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('total', 18, 2);
            $table->timestamps();

            $table->foreignId('budget_id')->references('id')->on('budgets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets_categories');
    }
};
