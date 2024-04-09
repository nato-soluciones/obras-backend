<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BanksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            ['code' => 'NACION_ARGENTINA', 'name' => 'Banco Nacion Argentina'],
            ['code' => 'PROVINCIA_BUENOS_AIRES', 'name' => 'Banco Provincia de Buenos Aires'],
            ['code' => 'GALICIA', 'name' => 'Banco Galicia'],
            ['code' => 'SANTANDER', 'name' => 'Banco Santander'],
            ['code' => 'BBVA', 'name' => 'Banco BBVA'],
            ['code' => 'CREDICOOP_COOP', 'name' => 'Banco Credicoop Coop'],
            ['code' => 'ICBC', 'name' => 'Banco ICBC'],
            ['code' => 'CIUDAD_BUENOS_AIRES', 'name' => 'Banco Ciudad de Buenos Aires'],
            ['code' => 'PATAGONIA', 'name' => 'Banco Patagonia'],
            ['code' => 'HSBC', 'name' => 'Banco HSBC'],
            ['code' => 'CITIBANK', 'name' => 'Banco Citibank'],
            ['code' => 'PROVINCIA_CORDOBA', 'name' => 'Banco Provincia de Cordoba'],
            ['code' => 'SUPERVIELLE', 'name' => 'Banco Supervielle'],
            ['code' => 'HIPOTECARIO', 'name' => 'Banco Hipotecario'],
            ['code' => 'BIND', 'name' => 'Banco Industrial']
        ];

        // Insertar datos en la tabla banks
        foreach ($banks as $bank) {
            Bank::create($bank);
        }
    }
}
