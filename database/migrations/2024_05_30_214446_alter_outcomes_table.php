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
        // Remover la restricción CHECK existente
        DB::statement("ALTER TABLE outcomes DROP CONSTRAINT outcomes_document_type_check");
        DB::statement("ALTER TABLE outcomes DROP CONSTRAINT outcomes_payment_method_check");
        DB::statement("ALTER TABLE outcomes DROP CONSTRAINT outcomes_type_check");


        Schema::table('outcomes', function (Blueprint $table) {
            // Cambia el tipo de columna de enum a string
            $table->string('type')->change();
            $table->string('document_type')->change();
            $table->string('payment_method')->change();
            $table->date('due_date')->nullable()->change();
            $table->dropColumn('provider_condition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outcomes', function (Blueprint $table) {
            // Cambia el tipo de columna de string a enum
            $table->enum('type', ['MATERIALS', 'CONTRACTORS', 'MANAGEMENT', 'OTHERS'])->change();
            $table->enum('document_type', ['BILL', 'REFERENCE', 'PAYMENT'])->change();
            $table->enum('payment_method', ['CASH', 'TRANSFER', 'CHECK', 'DEPOSIT', 'OTHER'])->change();
            $table->date('due_date')->nullable(false)->change();
            $table->string('provider_condition')->nullable()->change();
        });
    }
};
