<?php

namespace Database\Seeders;

use App\Models\CalendarEventCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CalendarEventCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Entrevista',
                'color' => '#3B82F6', // Azul
                'is_system' => true,
            ],
            [
                'name' => 'Reunión de Obra',
                'color' => '#EF4444', // Rojo
                'is_system' => true,
            ],
            [
                'name' => 'Inspección',
                'color' => '#F59E0B', // Amarillo/Naranja
                'is_system' => true,
            ],
            [
                'name' => 'Consulta Técnica',
                'color' => '#10B981', // Verde
                'is_system' => true,
            ],
            [
                'name' => 'Presentación',
                'color' => '#8B5CF6', // Púrpura
                'is_system' => true,
            ],
            [
                'name' => 'Seguimiento',
                'color' => '#F97316', // Naranja
                'is_system' => true,
            ],
        ];

        foreach ($categories as $category) {
            CalendarEventCategory::firstOrCreate(
                ['name' => $category['name'], 'is_system' => true],
                $category
            );
        }
    }
}
