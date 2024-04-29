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
        Schema::create('obra_daily_logs', function (Blueprint $table) {
            $table->id();
            $table->dateTimeTz('event_date');
            $table->string('file_name')->nullable();
            $table->text('comment')->nullable();
            $table->foreignId('created_by_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('obra_daily_log_tag_id')->constrained('obra_daily_log_tags')->onDelete('restrict');
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obra_daily_logs');
    }
};
