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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->date('date');
            $table->string('location');
            $table->string('email');
            $table->integer('exchange_rate');
            $table->integer('amount_usd');
            $table->integer('amount_ars');
            $table->string('amount_ars_text');
            $table->string('payment_concept');
            $table->text('comments')->nullable();

            $table->foreignId('obra_id')->references('id')->on('obras')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
