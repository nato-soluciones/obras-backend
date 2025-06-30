<?php

namespace Database\Seeders;

use App\Models\MeasurementUnit;
use Illuminate\Database\Seeder;

class MeasurementUnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Unidad', 'abbreviation' => 'uni', 'min_fraction' => 1.000, 'group' => 'uni'],
            ['name' => 'Metro', 'abbreviation' => 'm', 'min_fraction' => 0.001, 'group' => 'm'],
            ['name' => 'Metro cúbico', 'abbreviation' => 'm3', 'min_fraction' => 1.000, 'group' => 'm3'],
            ['name' => 'Kilogramo', 'abbreviation' => 'kg', 'min_fraction' => 0.001, 'group' => 'kg'],
            ['name' => 'Tonelada', 'abbreviation' => 't', 'min_fraction' => 1.000, 'group' => 'kg'],
            ['name' => 'Metro cuadrado', 'abbreviation' => 'm2', 'min_fraction' => 1.000, 'group' => 'm2'],
            ['name' => 'Par', 'abbreviation' => 'par', 'min_fraction' => 1.000, 'group' => 'par'],
            ['name' => 'Juego', 'abbreviation' => 'juego', 'min_fraction' => 1.000, 'group' => 'juego'],
            ['name' => 'Bolsa', 'abbreviation' => 'bolsa', 'min_fraction' => 1.000, 'group' => 'bolsa'],
            ['name' => 'Rollo', 'abbreviation' => 'rollo', 'min_fraction' => 1.000, 'group' => 'rollo'],
        ];

        foreach ($units as $unit) {
            MeasurementUnit::firstOrCreate($unit);
        }
    }
}
