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
        Schema::table('contractors', function (Blueprint $table) {
            $table->string('type')->default('contractor');
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('business_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contractors', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('last_name');
            $table->dropColumn('first_name');
            $table->string('business_name')->nullable(false);
        });
    }
};
