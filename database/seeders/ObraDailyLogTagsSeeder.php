<?php

namespace Database\Seeders;

use App\Models\ObraDailyLogTag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ObraDailyLogTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datos = [
            ['name' => 'Avance', 'color' => 'success'],
            ['name' => 'Actualización', 'color' => 'indigo'],
            ['name' => 'Evento externo', 'color' => 'warning'],
            ['name' => 'Error interno', 'color' => 'failure']
        ];

        foreach ($datos as $dato) {
            ObraDailyLogTag::firstOrCreate($dato);
        }
    }
}
