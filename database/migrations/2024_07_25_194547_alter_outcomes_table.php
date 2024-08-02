<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('outcomes', function (Blueprint $table) {
            $table->string('description')->nullable();
            $table->string('document_type')->nullable()->change();
            $table->string('order')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outcomes', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->string('document_type')->nullable(false)->change();
            $table->string('order')->nullable(false)->change();
        });
    }
};
