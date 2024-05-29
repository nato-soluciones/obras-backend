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
        Bank::truncate();

        $banks = [
            ['code' => 'NACION_ARGENTINA', 'name' => 'Banco Nación', 'is_virtual_wallet' => false],
            ['code' => 'PROVINCIA_BUENOS_AIRES', 'name' => 'Banco Provincia de Buenos Aires', 'is_virtual_wallet' => false],
            ['code' => 'PROVINCIA_SAN_JUAN', 'name' => 'Banco de San Juan', 'is_virtual_wallet' => false],
            ['code' => 'PROVINCIA_CHUBUT', 'name' => 'Banco del Chubut', 'is_virtual_wallet' => false],
            ['code' => 'PROVINCIA_SANTA_CRUZ', 'name' => 'Banco de Santa Cruz', 'is_virtual_wallet' => false],
            ['code' => 'PROVINCIA_LA_PAMPA', 'name' => 'Banco de La Pampa', 'is_virtual_wallet' => false],
            ['code' => 'PROVINCIA_CORRIENTES', 'name' => 'Banco de Corrientes', 'is_virtual_wallet' => false],
            ['code' => 'PROVINCIA_TIERRA_DEL_FUEGO', 'name' => 'Banco Provincia de Tierra del Fuego', 'is_virtual_wallet' => false],
            ['code' => 'PROVINCIA_FORMOSA', 'name' => 'Banco de Formosa', 'is_virtual_wallet' => false],
            ['code' => 'PROVINCIA_ENTRE_RIOS', 'name' => 'Nuevo Banco de Entre Ríos', 'is_virtual_wallet' => false],
            ['code' => 'PROVINCIA_SANTA_FE', 'name' => 'Nuevo Banco de Santa Fe', 'is_virtual_wallet' => false],
            ['code' => 'GALICIA', 'name' => 'Galicia', 'is_virtual_wallet' => false],
            ['code' => 'BBVA', 'name' => 'BBVA', 'is_virtual_wallet' => false],
            ['code' => 'ICBC', 'name' => 'ICBC', 'is_virtual_wallet' => false],
            ['code' => 'CITIBANK', 'name' => 'Citibank', 'is_virtual_wallet' => false],
            ['code' => 'SUPERVIELLE', 'name' => 'Supervielle', 'is_virtual_wallet' => false],
            ['code' => 'PATAGONIA', 'name' => 'Patagonia', 'is_virtual_wallet' => false],
            ['code' => 'HIPOTECARIO', 'name' => 'Hipotecario', 'is_virtual_wallet' => false],
            ['code' => 'SANTANDER', 'name' => 'Santander', 'is_virtual_wallet' => false],
            ['code' => 'CREDICOOP_COOP', 'name' => 'Credicoop Coop', 'is_virtual_wallet' => false],
            ['code' => 'CIUDAD_BUENOS_AIRES', 'name' => 'Ciudad de Buenos Aires', 'is_virtual_wallet' => false],
            ['code' => 'PROVINCIA_CORDOBA', 'name' => 'Provincia de Cordoba', 'is_virtual_wallet' => false],
            ['code' => 'HSBC', 'name' => 'HSBC', 'is_virtual_wallet' => false],
            ['code' => 'BIND', 'name' => 'Industrial', 'is_virtual_wallet' => false],
            ['code' => 'MACRO', 'name' => 'Macro', 'is_virtual_wallet' => false],
            ['code' => 'COMAFI', 'name' => 'Comafi', 'is_virtual_wallet' => false],
            ['code' => 'COLUMBIA', 'name' => 'Columbia', 'is_virtual_wallet' => false],
            ['code' => 'BRUBANK', 'name' => 'Brubank', 'is_virtual_wallet' => false],
            ['code' => 'OPEN_BANK_ARG', 'name' => 'Open Bank Argentina', 'is_virtual_wallet' => false],
            
            ['code' => 'PREX_ARG', 'name' => 'Prex Argentina', 'is_virtual_wallet' => true],
            ['code' => 'PERSONAL_PAY', 'name' => 'Personal Pay', 'is_virtual_wallet' => true],
            ['code' => 'UALA', 'name' => 'Ualá', 'is_virtual_wallet' => true],
            ['code' => 'MERCADO_PAGO', 'name' => 'Mercado Pago', 'is_virtual_wallet' => true],
            ['code' => 'CLARO_PAY', 'name' => 'Claro Pay', 'is_virtual_wallet' => true],
            ['code' => 'NARANJAX', 'name' => 'Naranja X', 'is_virtual_wallet' => true],

        ];

        // Insertar datos en la tabla banks
        foreach ($banks as $bank) {
            Bank::firstOrCreate($bank);
        }
    }
}
