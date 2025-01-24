<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $stores = [
            [
                'name' => 'Depósito Central',
                'description' => 'Almacén principal de materiales',
                'address' => 'Dirección del depósito central',
            ],
            [
                'name' => 'Depósito Secundario',
                'description' => 'Almacén secundario de materiales',
                'address' => 'Dirección del depósito secundario',
            ],
            [
                'name' => 'Depósito de Obra',
                'description' => 'Almacén temporal para obras en curso',
                'address' => 'Dirección del depósito de obra',
            ],
        ];

        foreach ($stores as $store) {
            Store::firstOrCreate($store);
        }
    }
}
