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
        Schema::table('outcomes', function (Blueprint $table) {
            $table->dropColumn('provider_cuit');
            $table->dropColumn('provider_name');
            $table->dropColumn('provider_iva');
            $table->dropColumn('due_date');

            $table->renameColumn('net_total', 'tax_total');
            $table->renameColumn('total', 'net_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outcomes', function (Blueprint $table) {
            $table->string('provider_cuit')->nullable();
            $table->string('provider_name')->nullable();
            $table->string('provider_iva')->nullable();
            $table->date('due_date')->nullable();

            $table->renameColumn('tax_total', 'net_total');
            $table->renameColumn('net_total', 'total');
        });
    }
};
