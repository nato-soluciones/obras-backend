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
        Schema::table('clients', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('budgets', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('obras', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('contractors', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('manufacturers', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('tools', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('incomes', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('outcomes', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('obras', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('contractors', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('manufacturers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('tools', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('outcomes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
