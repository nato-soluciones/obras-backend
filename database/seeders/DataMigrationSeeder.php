<?php

namespace Database\Seeders;

use App\Models\Obra;
use Illuminate\Support\Facades\DB;
use App\Models\Outcome;
use Illuminate\Database\Seeder;

class DataMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Este seed se ejecutará para hacer migraciones de datos cuando se vaya cambiando la estructura de la base de datos 
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Actualiza los registros con type = 'OTHERS' a 'OWNER_EXPENSES'
            Outcome::where('type', 'OTHERS')
                ->update(['type' => 'OWNER_EXPENSES']);

            // Actualiza los registros con type = 'MANAGEMENT' a 'PROJECT_EXPENSES'
            Outcome::where('type', 'MANAGEMENT')
                ->update(['type' => 'PROJECT_EXPENSES']);
 
            // Actualiza los registros con document_type = 'REFERENCE' a 'DELIVERY_NOTE'
            Outcome::where('document_type', 'REFERENCE')
                ->update(['document_type' => 'DELIVERY_NOTE']);

            // Actualiza los registros con document_type = 'PAYMENT' a 'RECEIPT'
            Outcome::where('document_type', 'PAYMENT')
                ->update(['document_type' => 'RECEIPT']);
        });

        DB::transaction(function () {
            // Recorre todas las obras y recupera datos del presupuesto y los guarda en la obra
            $obras = Obra::all();

            foreach ($obras as $obra) {
                $budget = $obra->budget;
                $obra->covered_area = $budget->covered_area;
                $obra->semi_covered_area = $budget->semi_covered_area;
                $obra->currency = $budget->currency;
                $obra->total = $budget->total;
                $obra->total_cost = $budget->total_cost;
                $obra->save();
            }
        });
    }
}
