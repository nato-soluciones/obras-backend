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
        Schema::table('materials', function (Blueprint $table) {
            $table->string('code')->nullable()->after('name');
            $table->string('dimensions')->nullable()->after('code');
            $table->string('quantity_per_package')->nullable()->after('dimensions');
            $table->string('color')->nullable()->after('quantity_per_package');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('dimensions');
            $table->dropColumn('quantity_per_package');
            $table->dropColumn('color');
        });
    }
};
