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
        Schema::create('role_relationships', function (Blueprint $table) {
            $table->unsignedBigInteger('user_role_id');
            $table->unsignedBigInteger('functional_role_id');
            $table->timestamps();

            $table->primary(['user_role_id', 'functional_role_id']);
            $table->foreign('user_role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('functional_role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_relationships');
    }
};
