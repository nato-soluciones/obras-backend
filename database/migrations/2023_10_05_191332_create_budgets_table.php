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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('code')->unique();

            $table->date('date');
            $table->foreignId('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->string('obra_name')->nullable();

            $table->date('estimated_time');
            $table->string('covered_area');
            $table->string('semi_covered_area');
            $table->enum('status', ['PENDING', 'APPROVED', 'DESAPPROVED', 'REQUOTE'])->default('PENDING');

            $table->integer('guilds_administrative')->nullable();
            $table->integer('guilds')->nullable();
            $table->integer('final_budget')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
