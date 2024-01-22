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
            $table->date('estimated_time');
            $table->string('obra_name');            
            $table->string('covered_area');
            $table->string('semi_covered_area');
            $table->enum('status', ['PENDING', 'APPROVED', 'DISAPPROVED', 'REQUOTE', 'FINISHED'])->default('PENDING');
            $table->longtext('comments')->nullable();
            $table->json('fields')->nullable();

            $table->foreignId('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
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
