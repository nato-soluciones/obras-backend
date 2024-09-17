<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('obra_stage_tasks', function (Blueprint $table) {
            $table->enum('progress_type', ['percentage', 'quantity'])->default('percentage');
            $table->decimal('progress', 5, 2)->nullable();
            $table->integer('max_quantity')->nullable();
            $table->integer('current_quantity')->nullable();
            $table->unsignedBigInteger('obra_stage_sub_stage_id')->nullable()->after('obra_stage_id');
            $table->foreign('obra_stage_sub_stage_id')->references('id')->on('obra_stage_sub_stages')->onDelete('cascade');
        });

        Schema::rename('obra_stage_tasks', 'obra_stage_sub_stage_tasks');
    }

    public function down()
    {
        Schema::rename('obra_stage_sub_stage_tasks', 'obra_stage_tasks');

        Schema::table('obra_stage_tasks', function (Blueprint $table) {
            $table->dropForeign(['obra_stage_sub_stage_id']);
            $table->dropColumn('obra_stage_sub_stage_id');
            $table->dropColumn('progress');
            $table->dropColumn('progress_type');
            $table->dropColumn('max_quantity');
            $table->dropColumn('current_quantity');
        });

    }
};
