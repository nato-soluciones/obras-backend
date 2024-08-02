<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Crear un SubStage por defecto para cada ObraStage
        DB::table('obra_stages')->orderBy('id')->chunk(100, function ($stages) {
            foreach ($stages as $stage) {
                $subStageId = DB::table('obra_stage_sub_stages')->insertGetId([
                    'name' => 'Default SubStage',
                    'progress' => $stage->progress,
                    'start_date' => $stage->start_date,
                    'end_date' => $stage->end_date,
                    'obra_stage_id' => $stage->id,
                    'created_by_id' => $stage->created_by_id,
                ]);

                // Actualizar todas las tareas asociadas a este ObraStage
                DB::table('obra_stage_sub_stage_tasks')
                ->where('obra_stage_id', $stage->id)
                    ->update(['obra_stage_sub_stage_id' => $subStageId]);
            }
        });
    }

    public function down()
    {
        // Revertir los cambios si es necesario
        DB::table('obra_stage_sub_stage_tasks')->update(['obra_stage_sub_stage_id' => null]);
        DB::table('obra_stage_sub_stages')->truncate();
    }
};
