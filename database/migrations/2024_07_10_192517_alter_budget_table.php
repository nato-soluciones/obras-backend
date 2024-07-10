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
        Schema::table('budgets', function (Blueprint $table) {
            $table->string('discount_type')->nullable();
            $table->string('discount_percentage', 8,2)->nullable();
            $table->decimal('discount_amount', 18, 2)->nullable();
            $table->string('discount_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropColumn('discount_type');
            $table->dropColumn('discount_percentage');
            $table->dropColumn('discount_amount');
            $table->dropColumn('discount_reason');
        });
    }
};
