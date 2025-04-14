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
            $table->enum('priority', ['baja', 'media', 'alta'])->default('baja');
            $table->boolean('is_resolved')->default(false);
            $table->datetime('date_resolved')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropColumn('priority');
            $table->dropColumn('is_resolved');
            $table->dropColumn('date_resolved');
        });
    }
};
