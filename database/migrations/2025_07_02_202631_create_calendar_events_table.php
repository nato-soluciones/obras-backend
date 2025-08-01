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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Organizador
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime');
            $table->integer('duration')->nullable(); // En minutos
            $table->string('location')->nullable();
            $table->enum('status', ['confirmed', 'cancelled', 'pending', 'rescheduled'])->default('confirmed');
            $table->text('notes')->nullable();
            $table->foreignId('calendar_event_category_id')->constrained()->onDelete('restrict');
            $table->string('source')->nullable(); // Origen del agendado
            $table->string('meeting_link')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'start_datetime']);
            $table->index(['start_datetime', 'end_datetime']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
