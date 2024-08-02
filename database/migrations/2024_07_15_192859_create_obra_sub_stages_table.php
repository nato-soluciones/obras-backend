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
        Schema::create('obra_stage_sub_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('progress', 5, 2)->default(0.00);
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedBigInteger('obra_stage_id');
            $table->foreign('obra_stage_id')->references('id')->on('obra_stages')->onDelete('cascade');
            $table->foreignId('created_by_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obra_stage_sub_stages');
    }
};
