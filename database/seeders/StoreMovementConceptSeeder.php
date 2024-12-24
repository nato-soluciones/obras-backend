<?php

namespace Database\Seeders;

use App\Models\StoreMovementConcept;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreMovementConceptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $concepts = [
            [
                'name' => 'Ingreso de material adquirido a proveedor',
                'description' => 'Se abastece stock de almacén con materiales nuevos',
            ],
            [
                'name' => 'Devolución',
                'description' => 'Material devuelto al proveedor',
            ],
            [
                'name' => 'Transferencia interna',
                'description' => 'Material trasladado entre almacenes internos',
            ],
        ];

        // Insertar o actualizar cada dato
        foreach ($concepts as $concept) {
            StoreMovementConcept::firstOrCreate($concept);
        }
    }
}
