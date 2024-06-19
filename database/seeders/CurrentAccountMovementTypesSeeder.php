<?php

namespace Database\Seeders;

use App\Models\CurrentAccountMovementType;
use Illuminate\Database\Seeder;

class CurrentAccountMovementTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movementsTypes = [
            ["name" => 'Notas de débito', "type" => 'DEBIT', 'active' => true, 'entity_type' => 'CLIENT', 'system_type' => false],
            ["name" => 'Notas de crédito ', "type" => 'CREDIT', 'active' => true, 'entity_type' => 'CLIENT', 'system_type' => false],
            ["name" => 'Devoluciones de mercadería', "type" => 'CREDIT', 'active' => true, 'entity_type' => 'CLIENT', 'system_type' => false],
            ["name" => 'Descuentos o Bonificaciones', "type" => 'CREDIT', 'active' => true, 'entity_type' => 'CLIENT', 'system_type' => false],
            ["name" => 'Otros débitos', "type" => 'DEBIT', 'active' => true, 'entity_type' => 'CLIENT', 'system_type' => false],
            ["name" => 'Otros créditos', "type" => 'CREDIT', 'active' => true, 'entity_type' => 'CLIENT', 'system_type' => false],
            ["name" => 'Intereses', "type" => 'DEBIT', 'active' => true, 'entity_type' => 'CLIENT', 'system_type' => false],
            ["name" => 'Proyecto', "type" => 'DEBIT', 'active' => true, 'entity_type' => 'CLIENT', 'system_type' => true],
            ["name" => 'Ingreso', "type" => 'CREDIT', 'active' => true, 'entity_type' => 'CLIENT', 'system_type' => true],
            ["name" => 'Egreso', "type" => 'DEBIT', 'active' => true, 'entity_type' => 'CLIENT', 'system_type' => true],
            ["name" => 'Ingreso - Eliminado', "type" => 'DEBIT', 'active' => true, 'entity_type' => 'CLIENT', 'system_type' => true],
            ["name" => 'Egreso - Eliminado', "type" => 'CREDIT', 'active' => true, 'entity_type' => 'CLIENT', 'system_type' => true],
            ["name" => 'Adicionales', "type" => 'DEBIT', 'active' => true, 'entity_type' => 'CLIENT', 'system_type' => true],
            ["name" => 'Adicionales - Eliminado', "type" => 'CREDIT', 'active' => true, 'entity_type' => 'CLIENT', 'system_type' => true],
            ["name" => 'Notas de débito', "type" => 'CREDIT', 'active' => true, 'entity_type' => 'PROVIDER', 'system_type' => false],
            ["name" => 'Notas de crédito ', "type" => 'DEBIT', 'active' => true, 'entity_type' => 'PROVIDER', 'system_type' => false],
            ["name" => 'Devoluciones de mercadería', "type" => 'DEBIT', 'active' => true, 'entity_type' => 'PROVIDER', 'system_type' => false],
            ["name" => 'Descuentos o Bonificaciones', "type" => 'DEBIT', 'active' => true, 'entity_type' => 'PROVIDER', 'system_type' => false],
            ["name" => 'Otros débitos', "type" => 'CREDIT', 'active' => true, 'entity_type' => 'PROVIDER', 'system_type' => false],
            ["name" => 'Otros créditos', "type" => 'DEBIT', 'active' => true, 'entity_type' => 'PROVIDER', 'system_type' => false],
            ["name" => 'Intereses', "type" => 'CREDIT', 'active' => true, 'entity_type' => 'PROVIDER', 'system_type' => false],
            ["name" => 'Proyecto', "type" => 'CREDIT', 'active' => true, 'entity_type' => 'PROVIDER', 'system_type' => true],
            ["name" => 'Ingreso', "type" => 'DEBIT', 'active' => true, 'entity_type' => 'PROVIDER', 'system_type' => true],
            ["name" => 'Egreso', "type" => 'CREDIT', 'active' => true, 'entity_type' => 'PROVIDER', 'system_type' => true],
            ["name" => 'Ingreso - Eliminado', "type" => 'CREDIT', 'active' => true, 'entity_type' => 'PROVIDER', 'system_type' => true],
            ["name" => 'Egreso - Eliminado', "type" => 'DEBIT', 'active' => true, 'entity_type' => 'PROVIDER', 'system_type' => true],
            ["name" => 'Adicionales', "type" => 'CREDIT', 'active' => true, 'entity_type' => 'PROVIDER', 'system_type' => true],
            ["name" => 'Adicionales - Eliminado', "type" => 'DEBIT', 'active' => true, 'entity_type' => 'PROVIDER', 'system_type' => true],
        ];

        // Insertar datos en la tabla ipc
        foreach ($movementsTypes as $row) {
            $existingIpc = CurrentAccountMovementType::where('name', $row['name'])->where('type', $row['type'])->where('entity_type', $row['entity_type'])->first();
            if (!$existingIpc) {
                CurrentAccountMovementType::create($row);
            }
        }
    }
}
