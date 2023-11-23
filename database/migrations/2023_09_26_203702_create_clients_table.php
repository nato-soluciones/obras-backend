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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('image')->nullable();

            $table->string('name');
            $table->string('company')->nullable();

            $table->string('address')->nullable();
            $table->string('department')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->string('cuit')->nullable();
            $table->string('condition')->nullable();
            $table->string('alicuota')->nullable();
            $table->string('invoice')->nullable();

            $table->string('comments')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
