<?php

namespace Database\Seeders;

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
            // Actualiza los registros con type = 'OTHER' a 'OWNER_EXPENSES'
            Outcome::where('type', 'OTHER')
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
    }
}
