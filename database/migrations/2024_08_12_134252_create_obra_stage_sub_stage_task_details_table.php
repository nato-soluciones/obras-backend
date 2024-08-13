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
        Schema::create('obra_stage_sub_stage_task_events', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('obra_stage_sub_stage_task_id');
            $table->timestamps();
            
            $table->foreign('obra_stage_sub_stage_task_id')->references('id')->on('obra_stage_sub_stage_tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obra_stage_sub_stage_task_events');
    }
};
