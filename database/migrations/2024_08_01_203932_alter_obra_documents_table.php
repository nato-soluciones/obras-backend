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
        Schema::rename('documents', 'obra_documents');

        Schema::table('obra_documents', function (Blueprint $table) {
            $table->string('storage_type')->default('file');
            $table->string('link')->nullable();
            $table->string('path')->nullable()->change();
            $table->foreignId('category_id')->nullable()->references('id')->on('obra_documents_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('obra_documents', 'documents');

        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('storage_type');
            $table->dropColumn('link');
            $table->dropColumn('category_id');
        });
    }
};
