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
        Schema::table('reminders', function (Blueprint $table) {
            $table->index(['user_id', 'datetime']);
            $table->index(['created_by', 'datetime']);
            $table->index(['user_id', 'is_resolved']);
            $table->index(['datetime', 'is_resolved']);
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'datetime']);
            $table->dropIndex(['created_by', 'datetime']);
            $table->dropIndex(['user_id', 'is_resolved']);
            $table->dropIndex(['datetime', 'is_resolved']);
            $table->dropIndex(['priority']);
        });
    }
};
