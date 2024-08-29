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
        Schema::create('obra_plan_charge_detail_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('obra_plan_charge_detail_id');
            $table->date('date');
            $table->decimal('amount', 18, 2);
            $table->string('description')->nullable();
            $table->unsignedBigInteger('created_by_id');
            
            $table->timestamps();
            $table->foreign('obra_plan_charge_detail_id')->references('id')->on('obra_plan_charge_details')->onDelete('cascade');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obra_plan_charge_detail_payments');
    }
};
