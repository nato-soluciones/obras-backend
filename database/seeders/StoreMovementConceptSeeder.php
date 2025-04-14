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
                'movement_type_id' => 2,
                'name' => 'Ingreso de material adquirido a proveedor',
                'description' => 'Se abastece stock de almacén con materiales nuevos',
            ],
            [
                'movement_type_id' => 2,
                'name' => 'Acopio de materiales',
                'description' => 'Se almacenan y organizan los materiales de construcción en el sitio de la obra antes de su uso',
            ],
            [
                'movement_type_id' => 3,
                'name' => 'Devolución',
                'description' => 'Material devuelto al proveedor',
            ],
            [
                'movement_type_id' => 3,
                'name' => 'Consumo',
                'description' => 'Reducción de inventario debido al uso de materiales',
            ],
            [
                'movement_type_id' => 3,
                'name' => 'Transferencia',
                'description' => 'Desplazamiento de materiales',
            ],
            [
                'movement_type_id' => 1,
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
