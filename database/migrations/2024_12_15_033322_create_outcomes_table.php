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
        Schema::create('outcomes', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['MATERIALS', 'CONTRACTORS', 'MANAGEMENT', 'OTHERS']);
            $table->string('cuit');
            $table->string('iva');
            $table->string('condition');

            $table->date('date');
            $table->date('due_date');
            $table->enum('document_type', ['BILL', 'REFERENCE', 'PAYMENT']);
            $table->string('order');
            $table->integer('gross_total');
            $table->integer('net_total');
            $table->integer('total');
            $table->date('payment_date');
            $table->enum('payment_method', ['CASH', 'TRANSFER', 'CHECK', 'DEPOSIT', 'OTHER']);

            $table->string('file')->nullable(); 
            $table->string('comments')->nullable();


            $table->foreignId('obra_id')->references('id')->on('obras')->onDelete('cascade');
            $table->foreignId('contractor_id')->references('id')->on('contractors')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outcomes');
    }
};
