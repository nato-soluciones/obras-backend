<?php

namespace Database\Seeders;

use App\Models\StoreMovementType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreMovementTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Transferencia',
                'description' => 'Movimiento de materiales entre almacenes internos',
            ],
            [
                'name' => 'Ingreso',
                'description' => 'Ingreso de materiales entregados por proveedor externo',
            ],
            [
                'name' => 'Salida',
                'description' => 'Baja de materiales del stock interno',
            ],
        ];

        // Insertar o actualizar cada dato
        foreach ($types as $type) {
            StoreMovementType::firstOrCreate($type);
        }
    }
}
