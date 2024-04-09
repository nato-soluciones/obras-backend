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
        Schema::table('outcomes', function (Blueprint $table) {
            $table->string('currency',5)->nullable();
            $table->decimal('exchange_rate', 18, 2)->nullable();
            $table->string('bank')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_cbu', 22)->nullable();
            $table->string('bank_alias')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('check_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outcomes', function (Blueprint $table) {
            $table->dropColumn('currency');
            $table->dropColumn('exchange_rate');
            $table->dropColumn('bank');
            $table->dropColumn('bank_name');
            $table->dropColumn('bank_cbu');
            $table->dropColumn('bank_alias');
            $table->dropColumn('bank_branch');
            $table->dropColumn('bank_account');
            $table->dropColumn('check_number');
        });
    }
};
