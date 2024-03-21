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
        $categories = [
            'Carpintaria',
            'Marcenaria',
            'Elétrica',
            'Hidráulica',
            'Pintura',
            'Alvenaria',
            'Jardinagem',
            'Limpeza',
            'Outros',
        ];

        foreach ($categories as $category) {
            ToolCategory::create([
                'name' => $category,
            ]);
        }
    }
}
