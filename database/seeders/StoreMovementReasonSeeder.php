<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreMovementReason;

class StoreMovementReasonSeeder extends Seeder
{
    public function run(): void
    {
        $reasons = [
            [
                'name' => 'Material dañado',
                'description' => 'El material llegó en mal estado o con daños visibles'
            ],
            [
                'name' => 'Cantidad incorrecta',
                'description' => 'La cantidad recibida no coincide con la cantidad indicada en la transferencia'
            ],
            [
                'name' => 'Material incorrecto',
                'description' => 'El material recibido no corresponde con lo solicitado'
            ],
            [
                'name' => 'Error en transferencia',
                'description' => 'La transferencia fue creada por error o con datos incorrectos'
            ],
            [
                'name' => 'Cambio de planes',
                'description' => 'Ya no se requiere el material en el destino'
            ],
            [
                'name' => 'Problemas de calidad',
                'description' => 'El material no cumple con los estándares de calidad requeridos'
            ],
            [
                'name' => 'Problemas logísticos',
                'description' => 'Problemas con el transporte o la logística de la transferencia'
            ],
            [
                'name' => 'Otro',
                'description' => 'Otros motivos no especificados'
            ]
        ];

        foreach ($reasons as $reason) {
            StoreMovementReason::create($reason);
        }
    }
} 