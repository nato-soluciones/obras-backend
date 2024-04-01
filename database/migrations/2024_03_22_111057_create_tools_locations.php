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
        Schema::create('tools_locations', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('location');
            $table->enum('status', ['IN_USE', 'UNDER_REPAIR', 'DAMAGED', 'LOST'])->default('IN_USE');
            $table->string('image')->nullable();
            $table->string('responsible');
            $table->text('comments')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('tool_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools_locations');
    }
};
