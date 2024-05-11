<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(ContractorIndustriesSeeder::class);
        $this->call(ToolCategorySeeder::class);
        // $this->call(ManufacturerCategories::class);
        $this->call(ObraDailyLogTagsSeeder::class);
        $this->call(BanksSeeder::class);
    }
}
