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
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('fields');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->bigInteger('code')->nullable();
            $table->json('fields')->nullable();
            $table->dropColumn('created_by_id');
        });
    }
};
