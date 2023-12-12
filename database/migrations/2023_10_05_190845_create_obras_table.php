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
        Schema::create('obras', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->string('name');
            $table->string('address');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['IN_PROGRESS', 'FINALIZED'])-default('IN_PROGRESS');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obras');
    }
};
