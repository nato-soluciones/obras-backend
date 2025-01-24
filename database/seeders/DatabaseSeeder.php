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
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(RelationalRolesPermissionsSeeder::class);
        $this->call(AppSettingsSeeder::class);
        $this->call(BanksSeeder::class);
        $this->call(ContractorIndustriesSeeder::class);
        $this->call(CurrentAccountMovementTypesSeeder::class);
        $this->call(IndicesSeeder::class);
        $this->call(MeasurementUnitsSeeder::class);
        $this->call(ObraDailyLogTagsSeeder::class);
        $this->call(StoreSeeder::class);
        $this->call(UserStoreSeeder::class);
        $this->call(MaterialSeeder::class);
        $this->call(StoreMovementStatusSeeder::class);
        $this->call(StoreMovementTypeSeeder::class);
        $this->call(StoreMovementConceptSeeder::class);
        $this->call(StoreMovementSeeder::class);
        $this->call(StoreMaterialSeeder::class);
        $this->call([
            StoreMovementReasonSeeder::class
        ]);
    }
}
