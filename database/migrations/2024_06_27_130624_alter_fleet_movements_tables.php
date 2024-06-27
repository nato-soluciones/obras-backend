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
        DB::statement('ALTER TABLE fleets DROP CONSTRAINT IF EXISTS fleets_type_check');
        DB::statement("ALTER TABLE fleets ADD CONSTRAINT fleets_type_check CHECK (((type)::text = ANY ((ARRAY['TRUCK'::character varying, 'MOTORCYCLE'::character varying, 'CAR'::character varying, 'PICKUP'::character varying, 'UTILITY'::character varying, 'OTHER'::character varying])::text[])))");
        
        DB::statement('ALTER TABLE fleets_movements DROP CONSTRAINT IF EXISTS fleets_movements_type_check');
        DB::statement("ALTER TABLE fleets_movements ADD CONSTRAINT fleets_movements_type_check CHECK (((type)::text = ANY ((ARRAY['MAINTENANCE'::character varying, 'REPAIR'::character varying])::text[])))");
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE fleets DROP CONSTRAINT IF EXISTS fleets_type_check');
        DB::statement("ALTER TABLE fleets ADD CONSTRAINT fleets_type_check CHECK (((type)::text = ANY ((ARRAY['TRUCK'::character varying, 'MOTORCYCLE'::character varying, 'CAR'::character varying, 'UTILITY'::character varying, 'OTHER'::character varying])::text[])))");

        DB::statement('ALTER TABLE fleets_movements DROP CONSTRAINT IF EXISTS fleets_movements_type_check');
        DB::statement("ALTER TABLE fleets_movements ADD CONSTRAINT fleets_movements_type_check CHECK (((type)::text = ANY ((ARRAY['MAINTENANCE'::character varying, 'UNDER_REPAIR'::character varying])::text[])))");
        
    }
};
