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
        Schema::create('cac', function (Blueprint $table) {
            $table->id();
            $table->string('period');
            $table->string('state');
            $table->decimal('value', 18, 2);
            $table->integer('inter_month_variation')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cac');
    }
};
