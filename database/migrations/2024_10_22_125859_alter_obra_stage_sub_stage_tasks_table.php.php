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
        Schema::table('obra_stage_sub_stage_tasks', function (Blueprint $table) {
            $table->boolean('has_quality_control')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obra_stage_sub_stage_tasks', function (Blueprint $table) {
            $table->dropColumn('has_quality_control');
        });
    }
};
