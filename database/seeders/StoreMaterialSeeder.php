<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Material;
use App\Models\StoreMaterial;
use Illuminate\Database\Seeder;

class StoreMaterialSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener todos los almacenes y materiales
        $stores = Store::all();
        $materials = Material::all();

        // Para cada almacén, crear algunos materiales con stock aleatorio
        foreach ($stores as $store) {
            // Seleccionar un número aleatorio de materiales para este almacén (entre 1 y 4)
            $numMaterials = rand(1, 4);
            $selectedMaterials = $materials->random($numMaterials);

            foreach ($selectedMaterials as $material) {
                StoreMaterial::create([
                    'store_id' => $store->id,
                    'material_id' => $material->id,
                    'quantity' => rand(10, 1000), // Stock aleatorio entre 10 y 1000
                    'minimum_limit' => rand(5, 50), // Stock mínimo aleatorio entre 5 y 50
                    'critical_limit' => rand(5, 50), // Stock mínimo aleatorio entre 5 y 50
                ]);
            }
        }
    }
} 