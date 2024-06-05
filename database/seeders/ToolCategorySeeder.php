<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ToolCategory;

class ToolCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TODO: Esto tiene sentido para todos los clientes? No parecen categorías muy específicas?
        $categories = [
            'Martillos',
            'Llaves',
            'Destornilladores',
            'Cinceles',
            'Sierras manuales',
            'Taladros',
            'Sierras eléctricas',
            'Lijadoras',
            'Amoladoras',
            'Mezcladoras de concreto',
            'Niveles',
            'Cintas métricas y reglas',
            'Teodolitos y estaciones totales',
            'Excavadoras',
            'Bulldozers',
            'Grúas',
            'Compactadoras',
        ];

        foreach ($categories as $category) {
            ToolCategory::firstOrCreate([
                'name' => $category,
            ]);
        }
    }
}
