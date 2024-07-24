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
        DB::transaction(
            function () {
                Schema::table('budgets', function (Blueprint $table) {
                    $table->renameColumn('obra_name', 'project_name');
                });

                Schema::table('obras', function (Blueprint $table) {
                    $table->string('covered_area')->default(0);
                    $table->string('semi_covered_area')->default(0);
                    $table->enum('currency', ['ARS', 'USD'])->default('ARS');
                    $table->decimal('total', 18, 2)->default(0.00);
                    $table->decimal( 'total_cost', 18, 2)->default(0.00);
                    $table->foreignId('budget_id')->nullable()->change();

                    $table->dropForeign(['client_id']);
                    $table->dropForeign(['budget_id']);

                    $table->foreign('client_id')
                        ->references('id')
                        ->on('clients')
                        ->onDelete('restrict');

                    $table->foreign('budget_id')
                        ->references('id')
                        ->on('budgets')
                        ->onDelete('restrict');
                });

                // Eliminamos el valor por defecto de covered_area
                DB::statement('ALTER TABLE obras ALTER COLUMN covered_area DROP DEFAULT');
                DB::statement('ALTER TABLE obras ALTER COLUMN semi_covered_area DROP DEFAULT');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->renameColumn('project_name', 'obra_name');
        });

        Schema::table('obras', function (Blueprint $table) {
            $table->dropColumn('covered_area');
            $table->dropColumn('semi_covered_area');
            $table->dropColumn('currency');
            $table->dropColumn('total');
            $table->dropColumn('total_cost');
        });

        Schema::table('obras', function (Blueprint $table) {
            // Desactivamos temporalmente las restricciones de clave foránea para esta tabla
            DB::statement('ALTER TABLE obras DISABLE TRIGGER ALL');

            $table->dropForeign(['client_id']);
            $table->dropForeign(['budget_id']);

            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');

            $table->foreign('budget_id')
                ->references('id')
                ->on('budgets')
                ->onDelete('cascade');

            // Reactivamos las restricciones
            DB::statement('ALTER TABLE obras ENABLE TRIGGER ALL');

            // Hacemos que budget_id no sea nullable de nuevo
            $table->foreignId('budget_id')->nullable(false)->change();
        });
    }
};
