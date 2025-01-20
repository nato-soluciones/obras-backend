<?php

namespace Database\Seeders;

use App\Models\StoreMovementStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreMovementStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Pendiente',
                'description' => 'Movimiento pendiente de aprobación por persona encargada',
            ],
            [
                'name' => 'Aprobado',
                'description' => 'Movimiento aprobado por persona encargada',
            ],
            [
                'name' => 'Rechazado',
                'description' => 'Movimiento rechazado por persona encargada',
            ],
            [
                'name' => 'Cancelado',
                'description' => 'Movimiento cancelado',
            ],
        ];

        // Insertar o actualizar cada dato
        foreach ($statuses as $status) {
            StoreMovementStatus::firstOrCreate($status);
        }
    }
}
