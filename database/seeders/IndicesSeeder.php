<?php

namespace Database\Seeders;

use App\Models\Auxiliaries\IndexType;
use App\Models\Cac;
use App\Models\Ipc;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IndicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $IPC = [
            ["period" => '2023-01', "value" => 6.0],
            ["period" => '2023-02', "value" => 6.6],
            ["period" => '2023-03', "value" => 7.7],
            ["period" => '2023-04', "value" => 8.40],
            ["period" => '2023-05', "value" => 7.80],
            ["period" => '2023-06', "value" => 6.00],
            ["period" => '2023-07', "value" => 6.30],
            ["period" => '2023-08', "value" => 12.40],
            ["period" => '2023-09', "value" => 12.70],
            ["period" => '2023-10', "value" => 8.30],
            ["period" => '2023-11', "value" => 12.80],
            ["period" => '2023-12', "value" => 25.50],
            ["period" => '2024-01', "value" => 20.60],
            ["period" => '2024-02', "value" => 13.20],
            ["period" => '2024-03', "value" => 11.00],
            ["period" => '2024-04', "value" => 8.80],
            ["period" => '2024-05', "value" => 4.20],
            ["period" => '2024-06', "value" => 4.60],
            ["period" => '2024-07', "value" => 4.00],
        ];

        $CAC = [
            ["period" => '2022-12', "general" => 2552.30, "materials" => 2933.60, "labour" => 1993.40],
            ["period" => '2023-01', "general" => 2713.10, "materials" => 3118.80, "labour" => 2125.60],
            ["period" => '2023-02', "general" => 2865.30, "materials" => 3276.50, "labour" => 2262.70],
            ["period" => '2023-03', "general" => 2998.70, "materials" => 3482.10, "labour" => 2290.20],
            ["period" => '2023-04', "general" => 3228.50, "materials" => 3740.90, "labour" => 2477.50],
            ["period" => '2023-05', "general" => 3448.30, "materials" => 3985.60, "labour" => 2660.90],
            ["period" => '2023-06', "general" => 3662.20, "materials" => 4282.60, "labour" => 2752.80],
            ["period" => '2023-07', "general" => 4001.90, "materials" => 4678.30, "labour" => 3010.50],
            ["period" => '2023-08', "general" => 4789.70, "materials" => 5707.80, "labour" => 3444.10],
            ["period" => '2023-09', "general" => 5219.90, "materials" => 6219.50, "labour" => 3744.90],
            ["period" => '2023-10', "general" => 5790.00, "materials" => 6979.60, "labour" => 4046.50],
            ["period" => '2023-11', "general" => 6785.20, "materials" => 8345.60, "labour" => 4498.00],
            ["period" => '2023-12', "general" => 9049.80, "materials" => 11806.70, "labour" => 5009.00],
            ["period" => '2024-01', "general" => 10331.00, "materials" => 13277.80, "labour" => 6011.70],
            ["period" => '2024-02', "general" => 11228.30, "materials" => 14214.40, "labour" => 6851.60],
            ["period" => '2024-03', "general" => 11669.00, "materials" => 14955.80, "labour" => 6851.60],
            ["period" => '2024-04', "general" => 12216.20, "materials" => 15226.80, "labour" => 7803.50],
            ["period" => '2024-05', "general" => 12747.40, "materials" => 15537.50, "labour" => 8657.90],
            ["period" => '2024-06', "general" => 13295.80, "materials" => 15811.70, "labour" => 9608.30],
            ["period" => '2024-07', "general" => 13574.80, "materials" => 16281.00, "labour" => 9608.30],
        ];

        $IndexTypes = [
            ['code' => 'IPC', 'name' => 'IPC'],
            ['code' => 'CAC_GENERAL', 'name' => 'CAC - General'],
            ['code' => 'CAC_MATERIALS', 'name' => 'CAC - Materiales'],
            ['code' => 'CAC_LABOUR', 'name' => 'CAC - Mano de obra'],

        ];


        // Insertar datos en la tabla ipc
        foreach ($IPC as $row) {
            $existingIpc = Ipc::where('period', $row['period'])->first();
            if (!$existingIpc) {
                Ipc::create($row);
            }
        }

        // Insertar datos en la tabla cac
        foreach ($CAC as $row) {
            $existingCac = Cac::where('period', $row['period'])->first();
            if (!$existingCac) {
                Cac::create($row);
            }
        }

        // Insertar datos en la tabla index_type
        foreach ($IndexTypes as $row) {
            $existingIndexType = IndexType::where('code', $row['code'])->first();
            if (!$existingIndexType) {
                IndexType::create($row);
            }
        }

    }
}
