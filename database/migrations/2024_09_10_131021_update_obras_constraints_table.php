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
    public function up(): void
    {
        // Elimina la restricción en la tabla obras
        DB::statement('ALTER TABLE obras DROP CONSTRAINT IF EXISTS obras_status_check');
        // Añade la nueva restricción en la tabla obras
        DB::statement("ALTER TABLE obras ADD CONSTRAINT obras_status_check CHECK (((status)::text = ANY ((ARRAY['IN_PROGRESS'::character varying, 'PAUSED'::character varying, 'FINALIZED'::character varying])::text[])))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE obras DROP CONSTRAINT IF EXISTS obras_status_check');
        DB::statement("ALTER TABLE obras ADD CONSTRAINT obras_status_check CHECK (((status)::text = ANY ((ARRAY['IN_PROGRESS'::character varying, 'FINALIZED'::character varying])::text[])))");
    }
};
