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
                'name' => 'Reunión',
                'color' => '#3B82F6',
                'is_system' => true,
            ],
            [
                'name' => 'Cita',
                'color' => '#10B981',
                'is_system' => true,
            ],
            [
                'name' => 'Evento',
                'color' => '#F59E0B',
                'is_system' => true,
            ],
            [
                'name' => 'Recordatorio',
                'color' => '#EF4444',
                'is_system' => true,
            ],
            [
                'name' => 'Personal',
                'color' => '#8B5CF6',
                'is_system' => true,
            ],
            [
                'name' => 'Trabajo',
                'color' => '#06B6D4',
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
