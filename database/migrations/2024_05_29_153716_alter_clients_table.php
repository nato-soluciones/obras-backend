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
            $table->dropColumn('code');
            $table->dropColumn('image');
            $table->dropColumn('company');
            $table->dropColumn('alicuota');
            $table->dropColumn('invoice');
            $table->dropColumn('status');

            $table->string('person_type')->default('individual');
            $table->string('lastname')->nullable();
            $table->string('name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('person_type');
            $table->dropColumn('lastname');
        });
    }
};
