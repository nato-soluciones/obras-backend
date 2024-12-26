<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\MeasurementUnit;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener unidades de medida existentes
        $units = MeasurementUnit::all();
        
        if ($units->isEmpty()) {
            return;
        }

        $materials = [
            [
                'name' => 'Cemento',
                'description' => 'Cemento portland',
                'measurement_unit_id' => $units->where('abbreviation', 'kg')->first()?->id ?? $units->first()->id,
            ],
            [
                'name' => 'Arena',
                'description' => 'Arena fina para construcción',
                'measurement_unit_id' => $units->where('abbreviation', 'm³')->first()?->id ?? $units->first()->id,
            ],
            [
                'name' => 'Ladrillos',
                'description' => 'Ladrillos comunes',
                'measurement_unit_id' => $units->where('abbreviation', 'u')->first()?->id ?? $units->first()->id,
            ],
            [
                'name' => 'Hierro',
                'description' => 'Hierro para construcción',
                'measurement_unit_id' => $units->where('abbreviation', 'kg')->first()?->id ?? $units->first()->id,
            ],
        ];

        foreach ($materials as $material) {
            Material::firstOrCreate($material);
        }
    }
}
