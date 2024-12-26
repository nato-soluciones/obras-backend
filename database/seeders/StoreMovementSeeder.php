<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Material;
use App\Models\StoreMovement;
use App\Models\StoreMovementStatus;
use App\Models\StoreMovementType;
use App\Models\StoreMovementConcept;
use App\Models\User;
use Illuminate\Database\Seeder;

class StoreMovementSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener datos necesarios
        $users = User::all();
        $stores = Store::all();
        $materials = Material::all();
        $pendingStatus = StoreMovementStatus::where('name', 'Pendiente')->first();
        $approvedStatus = StoreMovementStatus::where('name', 'Aprobado')->first();
        $transferType = StoreMovementType::where('name', 'Transferencia')->first();
        $inputType = StoreMovementType::where('name', 'Ingreso')->first();
        $outputType = StoreMovementType::where('name', 'Salida')->first();
        $concepts = StoreMovementConcept::all();

        // Crear algunos movimientos de ejemplo
        $movements = [
            // Transferencia entre almacenes
            [
                'created_by_id' => $users->random()->id,
                'from_store_id' => $stores->first()->id,
                'to_store_id' => $stores->last()->id,
                'store_movement_type_id' => $transferType->id,
                'store_movement_status_id' => $pendingStatus->id,
                'store_movement_concept_id' => $concepts->where('movement_type_id', $transferType->id)->first()->id,
            ],
            // Ingreso de material
            [
                'created_by_id' => $users->random()->id,
                'from_store_id' => $stores->random()->id,
                'to_store_id' => $stores->random()->id,
                'store_movement_type_id' => $inputType->id,
                'store_movement_status_id' => $approvedStatus->id,
                'store_movement_concept_id' => $concepts->where('movement_type_id', $inputType->id)->first()->id,
            ],
            // Salida de material
            [
                'created_by_id' => $users->random()->id,
                'from_store_id' => $stores->random()->id,
                'to_store_id' => $stores->random()->id,
                'store_movement_type_id' => $outputType->id,
                'store_movement_status_id' => $pendingStatus->id,
                'store_movement_concept_id' => $concepts->where('movement_type_id', $outputType->id)->first()->id,
            ],
        ];

        // Insertar los movimientos y sus materiales
        foreach ($movements as $movement) {
            $storeMovement = StoreMovement::create($movement);
            
            // Crear entre 1 y 3 materiales por movimiento
            $numMaterials = rand(1, 3);
            for ($i = 0; $i < $numMaterials; $i++) {
                $storeMovement->movementMaterials()->create([
                    'material_id' => $materials->random()->id,
                    'quantity' => rand(1, 100)
                ]);
            }
        }
    }
} 