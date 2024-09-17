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
        Schema::create('company_cost_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('company_costs', function (Blueprint $table) {
            $table->id();
            $table->date('registration_date');
            $table->string('period', 10);
            $table->string('description');
            $table->decimal('amount', 18, 2);
            $table->enum('payment_status', ['PENDING', 'PAID', 'PARTIALLY_PAID'])->default('PENDING');
            $table->date('payment_date')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('responsible_id');
            $table->unsignedBigInteger('created_by_id');

            $table->foreign('category_id')->references('id')->on('company_cost_categories')->onDelete('restrict');
            $table->foreign('responsible_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_costs');
        Schema::dropIfExists('company_cost_categories');
    }
};
