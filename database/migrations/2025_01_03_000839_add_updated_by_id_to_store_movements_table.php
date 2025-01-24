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
        Schema::table('store_movements', function (Blueprint $table) {
            $table->foreignId('updated_by_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_movements', function (Blueprint $table) {
            $table->dropForeign(['updated_by_id']);
            $table->dropColumn('updated_by_id');
        });
    }
};
