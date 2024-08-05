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
            $table->string('covered_area')->nullable()->change();
            $table->string('semi_covered_area')->nullable()->change();
        });

        Schema::table('obras', function (Blueprint $table) {
            $table->string('covered_area')->nullable()->change();
            $table->string('semi_covered_area')->nullable()->change();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->string('covered_area')->nullable(false)->change();
            $table->string('semi_covered_area')->nullable(false)->change();
        });

        Schema::table('obras', function (Blueprint $table) {
            $table->string('covered_area')->nullable(false)->change();
            $table->string('semi_covered_area')->nullable(false)->change();
        });
    }
};
