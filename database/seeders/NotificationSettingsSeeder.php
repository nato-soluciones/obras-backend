<?php

namespace Database\Seeders;

use App\Models\NotificationSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['code'=> 'obraStageFinished' ,'group'=> 'obraStages', 'title' => 'Etapa Finalizada', 'description' => 'Da aviso cuando se finalice una etapa', 'priority' => 'baja'],
            ['code'=> 'obraStageSoonToExpire','group'=> 'obraStages', 'title' => 'Etapa pronta a vencer', 'description' => 'Da aviso cuando una etapa esté pronta a vencer', 'priority' => 'media'],
            ['code'=> 'obraStageExpired','group'=> 'obraStages', 'title' => 'Etapa vencida', 'description' => 'Da aviso cuando una etapa está vencida', 'priority' => 'alta'],
        ];

        foreach ($settings as $setting) {
            NotificationSetting::firstOrCreate($setting);
        }
    }
}
