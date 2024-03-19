<?php

namespace Database\Seeders;

use App\Models\ContractorIndustry;
use Illuminate\Database\Seeder;

class ContractorIndustriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = [
            ['code' => 'CONSTRUCTION', 'name' => 'Construcción'],
            ['code' => 'ELECTRICAL', 'name' => 'Electricidad'],
            ['code' => 'PLUMBING', 'name' => 'Plomeria'],
            ['code' => 'HVAC', 'name' => 'Ventilacion'],
            ['code' => 'PAINTING', 'name' => 'Pintura'],
            ['code' => 'LANDSCAPING', 'name' => 'Paisajismo'],
            ['code' => 'ROOFING', 'name' => 'Techista'],
            ['code' => 'SECURITY', 'name' => 'Seguridad'],
            ['code' => 'INTERIOR_REMODELING', 'name' => 'Remodelación de Interiores'],
            ['code' => 'DEMOLITION', 'name' => 'Demolición'],
            ['code' => 'CIVIL_ENGINEERING', 'name' => 'Ingeniería Civil'],
            ['code' => 'ENERGY_CONSULTING', 'name' => 'Consultoría en Energía'],
            ['code' => 'HOME_AUTOMATION', 'name' => 'Automatización del Hogar'],
            ['code' => 'CARPENTRY', 'name' => 'Carpintería']
        ];

        // Insertar datos en la tabla contractor_industries
        foreach ($industries as $industry) {
            ContractorIndustry::create($industry);
        }
    }
}
