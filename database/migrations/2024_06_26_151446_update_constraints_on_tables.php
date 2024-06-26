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
        // Elimina la restricción en la tabla tools
        DB::statement('ALTER TABLE tools DROP CONSTRAINT IF EXISTS tools_status_check');
        // Añade la nueva restricción en la tabla tools
        DB::statement("ALTER TABLE tools ADD CONSTRAINT tools_status_check CHECK (((status)::text = ANY ((ARRAY['NEW'::character varying, 'IN_USE'::character varying, 'UNDER_REPAIR'::character varying, 'DAMAGED'::character varying, 'LOST'::character varying])::text[])))");
        
        // Elimina la restricción en la tabla contacts
        DB::statement('ALTER TABLE contacts DROP CONSTRAINT IF EXISTS contacts_type_check');
        // Añade la nueva restricción en la tabla contacts
        DB::statement("ALTER TABLE contacts ADD CONSTRAINT contacts_type_check CHECK (((type)::text = ANY ((ARRAY['ARCHITECT'::character varying, 'CLIENT'::character varying, 'PROVIDER'::character varying, 'OTHER'::character varying])::text[])))");

        Schema::table('budgets', function (Blueprint $table) {
            $table->date('expiration_date')->nullable();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE tools DROP CONSTRAINT IF EXISTS tools_status_check');
        DB::statement("ALTER TABLE tools ADD CONSTRAINT tools_status_check CHECK (((status)::text = ANY ((ARRAY['IN_USE'::character varying, 'UNDER_REPAIR'::character varying, 'DAMAGED'::character varying, 'LOST'::character varying])::text[])))");
        
        DB::statement('ALTER TABLE contacts DROP CONSTRAINT IF EXISTS contacts_type_check');
        DB::statement("ALTER TABLE contacts ADD CONSTRAINT contacts_type_check CHECK (((type)::text = ANY ((ARRAY['CLIENT'::character varying, 'PROVIDER'::character varying, 'OTHER'::character varying])::text[])))");

        Schema::table('budgets', function (Blueprint $table) {
            $table->dropColumn('expiration_date');
        });
    }
};
