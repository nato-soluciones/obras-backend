<?php

namespace Database\Seeders;

use App\Models\AppSetting;
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
            ['module' => 'FLEETS', 'key' => 'SHOW_IMAGE_TABLE', 'value' => 'false', 'type' => 'boolean', 'description' => 'Muestra o no la imagen en la tabla'],
            ['module' => 'OBRAS', 'key' => 'STAGE_TO_OVERCOME', 'value' => '5', 'type' => 'number', 'description' => 'Indica cuantos días antes del vencimiento una etapa se marca como por vencer'],
            ['module' => 'WEATHER', 'key' => 'LOCATION_URL', 'value' => 'https://forecast7.com/es/n38d01n57d54/mar-del-plata/', 'type' => 'string', 'description' => 'URL de la ubicación del clima'],
            ['module' => 'WEATHER', 'key' => 'LOCATION_NAME', 'value' => 'MAR DEL PLATA', 'type' => 'string', 'description' => 'Nombre a mostrar en la ubicación del clima'],
        ];

        // Insertar datos en la tabla contractor_industries
        foreach ($settings as $setting) {
            $exist = AppSetting::where('module', $setting['module'])->where('key', $setting['key'])->first();
            if (!$exist) {
                AppSetting::firstOrCreate($setting);
            }
        }
    }
}
