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
        Schema::create('obra_plan_charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('obra_id');
            $table->decimal('financed_amount', 18, 2);
            $table->unsignedInteger('installment_count');
            $table->enum('installment_frequency', ['DAILY','WEEKLY','BIWEEKLY','MONTHLY']);
            $table->date('installment_first_due_date')->comment('Fecha de vencimiento de la primer cuota');
            $table->unsignedBigInteger('created_by_id');
            $table->timestamps();
            
            $table->foreign('obra_id')->references('id')->on('obras')->onDelete('cascade');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obra_plan_charges');
    }
};
