<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ManufacturerCategory;

class ManufacturerCategories extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Frío',
        ];

        foreach ($categories as $category) {
            ManufacturerCategory::firstOrCreate([
                'name' => $category,
            ]);
        }
    }
}
