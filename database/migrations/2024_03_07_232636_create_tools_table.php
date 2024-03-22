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
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand');
            $table->decimal('value', 18, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->enum('status', ['IN_USE', 'UNDER_REPAIR', 'DAMAGED', 'LOST'])->default('IN_USE');
            $table->date('last_maintenance')->nullable();
            
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            
            $table->foreignId('category_id')->references('id')->on('tools_categories')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
