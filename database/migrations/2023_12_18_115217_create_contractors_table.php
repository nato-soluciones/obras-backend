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
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('industry');
            $table->string('address');
            $table->string('zip');
            $table->string('city');
            $table->string('web')->nullable();
            $table->string('email')->nullable();
            $table->string('referral')->nullable();
            $table->string('position')->nullable();
            $table->string('phone')->nullable();

            $table->string('cuit')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractors');
    }
};
