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
        Schema::create('index_types', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->timestamps();
        });


        Schema::create('obra_plan_charge_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('obra_plan_charge_id');
            $table->enum('type', ['INSTALLMENT', 'ADJUSTMENT'])->default('INSTALLMENT');
            $table->unsignedInteger('installment_number')->nullable();
            $table->string('concept');
            $table->string('description')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedInteger('index_type')->nullable();
            $table->string('index_period')->nullable();
            $table->decimal('installment_amount', 18, 2);
            $table->decimal('adjustment_amount', 18, 2)->nullable();
            $table->decimal('total_amount', 18, 2);
            $table->date('full_payment_date')->nullable();
            $table->enum('status', ['PENDING', 'PAID', 'PARTIALLY_PAID'])->default('PENDING');
            $table->timestamps();

            $table->foreign('index_type')->references('id')->on('index_types')->onDelete('restrict');
            $table->foreign('obra_plan_charge_id')->references('id')->on('obra_plan_charges')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obra_plan_charge_details');
        Schema::dropIfExists('index_types');
    }
};
