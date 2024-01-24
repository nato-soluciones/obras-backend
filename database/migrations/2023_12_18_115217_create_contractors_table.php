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
            $table->string('address')->nullable();
            $table->string('zip')->nullable();
            $table->string('city')->nullable();
            $table->string('web')->nullable();
            $table->string('email')->nullable();
            $table->string('referral')->nullable();
            $table->string('position')->nullable();
            $table->string('phone')->nullable();

            $table->string('cuit')->nullable();
            $table->string('condition')->nullable();
            $table->string('alicuota')->nullable();

            $table->string('bank')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_type')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_cbu')->nullable();
            $table->string('bank_alias')->nullable();

            $table->longtext('comments')->nullable();
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
