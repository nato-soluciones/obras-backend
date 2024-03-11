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
        Schema::table('additionals', function (Blueprint $table) {
            $table->decimal('total', 18, 2)->default(0.00)->change();
            $table->decimal('total_cost', 18, 2)->default(0.00)->change();
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->decimal('total', 18, 2)->default(0.00)->change();
            $table->decimal('total_cost', 18, 2)->default(0.00)->change();
        });

        Schema::table('incomes', function(Blueprint $table) {
            $table->decimal('amount_usd', 18, 2)->default(0.00)->change();
            $table->decimal('amount_ars', 18, 2)->default(0.00)->change();
        });

        Schema::table('outcomes', function (Blueprint $table) {
            $table->decimal('gross_total', 18, 2)->default(0.00)->change();
            $table->decimal('net_total', 18, 2)->default(0.00)->change();
            $table->decimal('total', 18, 2)->default(0.00)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('additionals', function (Blueprint $table) {
            $table->integer('total')->default(0);
            $table->integer('total_cost')->default(0);
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->integer('total')->default(0)->change();
            $table->integer('total_cost')->default(0)->change();
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->integer('amount_usd')->default(0)->change();
            $table->integer('amount_ars')->default(0)->change();
        });

        Schema::table('outcomes', function (Blueprint $table) {
            $table->integer('gross_total')->default(0)->change();
            $table->integer('net_total')->default(0)->change();
            $table->integer('total')->default(0)->change();
        });
    }
};
