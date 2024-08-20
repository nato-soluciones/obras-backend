<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['module' => 'TOOLS', 'key' => 'SHOW_IMAGE_TABLE', 'value' => 'false', 'type' => 'boolean', 'description' => 'Muestra o no la imagen en la tabla'],
            ['module' => 'FLEET', 'key' => 'SHOW_IMAGE_TABLE', 'value' => 'false', 'type' => 'boolean', 'description' => 'Muestra o no la imagen en la tabla'],
        ];

        // Insertar datos en la tabla contractor_industries
        foreach ($settings as $setting) {
            AppSetting::firstOrCreate($setting);
        }
    }
}
