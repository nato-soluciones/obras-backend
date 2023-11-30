<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('code')->unique();

            $table->date('date');
            $table->foreignId('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->string('obra_name')->nullable();

            $table->date('estimated_time');
            $table->string('covered_area');
            $table->string('semi_covered_area');
            $table->enum('status', ['PENDING', 'APPROVED', 'DESAPPROVED', 'REQUOTE']);

            // Tareas preliminares
            $table->integer('preliminary_tasks_final')->nullable();

            $table->integer('palisade_assembly_lm')->nullable();
            $table->integer('palisade_assembly_lm_unit')->nullable();
            $table->integer('palisade_assembly_lm_budget')->nullable();

            $table->integer('assembly_workshop')->nullable();
            $table->integer('assembly_workshop_unit')->nullable();
            $table->integer('assembly_workshop_budget')->nullable();

            $table->integer('work_poster')->nullable();
            $table->integer('work_poster_unit')->nullable();
            $table->integer('work_poster_budget')->nullable();

            $table->integer('electrical_panel')->nullable();
            $table->integer('electrical_panel_unit')->nullable();
            $table->integer('electrical_panel_budget')->nullable();

            $table->integer('work_pillar')->nullable();
            $table->integer('work_pillar_unit')->nullable();
            $table->integer('work_pillar_budget')->nullable();

            $table->integer('demolitions')->nullable();
            $table->integer('demolitions_unit')->nullable();
            $table->integer('demolitions_budget')->nullable();

            // Movimiento de suelos
            $table->integer('earthworks_final')->nullable();

            $table->integer('adaptation_land')->nullable();
            $table->integer('adaptation_land_unit')->nullable();
            $table->integer('adaptation_land_budget')->nullable();

            $table->integer('removal_topsoil')->nullable();
            $table->integer('removal_topsoil_unit')->nullable();
            $table->integer('removal_topsoil_budget')->nullable();

            $table->integer('leveling')->nullable();
            $table->integer('leveling_unit')->nullable();
            $table->integer('leveling_budget')->nullable();

            $table->integer('excavation')->nullable();
            $table->integer('excavation_unit')->nullable();
            $table->integer('excavation_budget')->nullable();

            $table->integer('land_contribution')->nullable();
            $table->integer('land_contribution_unit')->nullable();
            $table->integer('land_contribution_budget')->nullable();

            // Estucturas de H
            $table->integer('h_structures_final')->nullable();

            $table->integer('structure_rethinking')->nullable();
            $table->integer('structure_rethinking_unit')->nullable();
            $table->integer('structure_rethinking_budget')->nullable();

            $table->integer('isolated_bases')->nullable();
            $table->integer('isolated_bases_unit')->nullable();
            $table->integer('isolated_bases_budget')->nullable();

            $table->integer('piles')->nullable();
            $table->integer('piles_unit')->nullable();
            $table->integer('piles_budget')->nullable();

            $table->integer('platea')->nullable();
            $table->integer('platea_unit')->nullable();
            $table->integer('platea_budget')->nullable();

            $table->integer('retaining_wall')->nullable();
            $table->integer('retaining_wall_unit')->nullable();
            $table->integer('retaining_wall_budget')->nullable();

            $table->integer('submural_partition')->nullable();
            $table->integer('submural_partition_unit')->nullable();
            $table->integer('submural_partition_budget')->nullable();

            $table->integer('foundation_beams')->nullable();
            $table->integer('foundation_beams_unit')->nullable();
            $table->integer('foundation_beams_budget')->nullable();

            $table->integer('isolated_columns')->nullable();
            $table->integer('isolated_columns_unit')->nullable();
            $table->integer('isolated_columns_budget')->nullable();

            $table->integer('insulated_beams')->nullable();
            $table->integer('insulated_beams_unit')->nullable();
            $table->integer('insulated_beams_budget')->nullable();

            $table->integer('seen_partitions')->nullable();
            $table->integer('seen_partitions_unit')->nullable();
            $table->integer('seen_partitions_budget')->nullable();

            $table->integer('unseen_partitions')->nullable();
            $table->integer('unseen_partitions_unit')->nullable();
            $table->integer('unseen_partitions_budget')->nullable();

            $table->integer('exposed_slab')->nullable();
            $table->integer('exposed_slab_unit')->nullable();
            $table->integer('exposed_slab_budget')->nullable();

            $table->integer('common_slab_unseen')->nullable();
            $table->integer('common_slab_unseen_unit')->nullable();
            $table->integer('common_slab_unseen_budget')->nullable();

            $table->integer('flight_stairs_seen')->nullable();
            $table->integer('flight_stairs_seen_unit')->nullable();
            $table->integer('flight_stairs_seen_budget')->nullable();

            $table->integer('flight_stairs_unseen')->nullable();
            $table->integer('flight_stairs_unseen_unit')->nullable();
            $table->integer('flight_stairs_unseen_budget')->nullable();

            $table->integer('cleaning_concrete')->nullable();
            $table->integer('cleaning_concrete_unit')->nullable();
            $table->integer('cleaning_concrete_budget')->nullable();

            $table->integer('concrete_makeup')->nullable();
            $table->integer('concrete_makeup_unit')->nullable();
            $table->integer('concrete_makeup_budget')->nullable();

            // Albañilería
            $table->integer('masonry_final')->nullable();
            
            $table->integer('masonry_layout')->nullable();
            $table->integer('masonry_layout_unit')->nullable();
            $table->integer('masonry_layout_budget')->nullable();

            $table->integer('leveling_masonry')->nullable();
            $table->integer('leveling_masonry_unit')->nullable();
            $table->integer('leveling_masonry_budget')->nullable();

            $table->integer('double_perimeter_masonry')->nullable();
            $table->integer('double_perimeter_masonry_unit')->nullable();
            $table->integer('double_perimeter_masonry_budget')->nullable();

            $table->integer('lh8_masonry')->nullable();
            $table->integer('lh8_masonry_unit')->nullable();
            $table->integer('lh8_masonry_budget')->nullable();

            $table->integer('lh12_masonry')->nullable();
            $table->integer('lh12_masonry_unit')->nullable();
            $table->integer('lh12_masonry_budget')->nullable();

            $table->integer('lh18_masonry')->nullable();
            $table->integer('lh18_masonry_unit')->nullable();
            $table->integer('lh18_masonry_budget')->nullable();

            $table->integer('common_brick_masonry')->nullable();
            $table->integer('common_brick_masonry_unit')->nullable();
            $table->integer('common_brick_masonry_budget')->nullable();

            $table->integer('interior_placement')->nullable();
            $table->integer('interior_placement_unit')->nullable();
            $table->integer('interior_placement_budget')->nullable();

            $table->integer('exterior_placement')->nullable();
            $table->integer('exterior_placement_unit')->nullable();
            $table->integer('exterior_placement_budget')->nullable();

            $table->integer('grill_execution')->nullable();
            $table->integer('grill_execution_unit')->nullable();
            $table->integer('grill_execution_budget')->nullable();

            $table->integer('thick_plaster')->nullable();
            $table->integer('thick_plaster_unit')->nullable();
            $table->integer('thick_plaster_budget')->nullable();

            $table->integer('exterior_thick_plaster')->nullable();
            $table->integer('exterior_thick_plaster_unit')->nullable();
            $table->integer('exterior_thick_plaster_budget')->nullable();

            $table->integer('plaster_exterior')->nullable();
            $table->integer('plaster_exterior_unit')->nullable();
            $table->integer('plaster_exterior_budget')->nullable();

            $table->integer('h10_interior')->nullable();
            $table->integer('h10_interior_unit')->nullable();
            $table->integer('h10_interior_budget')->nullable();

            $table->integer('h10_exterior_terrain')->nullable();
            $table->integer('h10_exterior_terrain_unit')->nullable();
            $table->integer('h10_exterior_terrain_budget')->nullable();

            $table->integer('h10_exterior_slab')->nullable();
            $table->integer('h10_exterior_slab_unit')->nullable();
            $table->integer('h10_exterior_slab_budget')->nullable();

            $table->integer('h4_interior')->nullable();
            $table->integer('h4_interior_unit')->nullable();
            $table->integer('h4_interior_budget')->nullable();

            $table->integer('h4_exterior_terrain')->nullable();
            $table->integer('h4_exterior_terrain_unit')->nullable();
            $table->integer('h4_exterior_terrain_budget')->nullable();

            $table->integer('h4_exterior_slab')->nullable();
            $table->integer('h4_exterior_slab_unit')->nullable();
            $table->integer('h4_exterior_slab_budget')->nullable();

            $table->integer('h4_guild_help')->nullable();
            $table->integer('h4_guild_help_unit')->nullable();
            $table->integer('h4_guild_help_budget')->nullable();

            $table->integer('h4_periodic_cleaning')->nullable();
            $table->integer('h4_periodic_cleaning_unit')->nullable();
            $table->integer('h4_periodic_cleaning_budget')->nullable();

            $table->integer('h4_final_cleaning')->nullable();
            $table->integer('h4_final_cleaning_unit')->nullable();
            $table->integer('h4_final_cleaning_budget')->nullable();

            // Instalacion sanitaria
            $table->integer('sanitary_installation_final')->nullable();

            // Pre Instalaciones de aires acondicionados
            $table->integer('pre_air_conditioning_final')->nullable();

            $table->integer('pre_air_conditioning_cleaning')->nullable();
            $table->integer('pre_air_conditioning_cleaning_unit')->nullable();
            $table->integer('pre_air_conditioning_cleaning_budget')->nullable();

            // Calefacción por piso radiante
            $table->integer('underfloor_heating_final')->nullable();

            $table->integer('underfloor_heating')->nullable();
            $table->integer('underfloor_heating_unit')->nullable();
            $table->integer('underfloor_heating_budget')->nullable();

            $table->integer('radiator_heating')->nullable();
            $table->integer('radiator_heating_unit')->nullable();
            $table->integer('radiator_heating_budget')->nullable();

            // Instalación eléctrica
            $table->integer('electrical_installation_final')->nullable();

            // Durlock
            $table->integer('durlock_total')->nullable();

            $table->integer('alpress_plaster')->nullable();
            $table->integer('alpress_plaster_unit')->nullable();
            $table->integer('alpress_plaster_budget')->nullable();

            $table->integer('corner_pieces')->nullable();
            $table->integer('corner_pieces_unit')->nullable();
            $table->integer('corner_pieces_budget')->nullable();

            $table->integer('masonry_partition')->nullable();
            $table->integer('masonry_partition_unit')->nullable();
            $table->integer('masonry_partition_budget')->nullable();

            $table->integer('reinforced_ceiling')->nullable();
            $table->integer('reinforced_ceiling_unit')->nullable();
            $table->integer('reinforced_ceiling_budget')->nullable();

            $table->integer('wooden_ceiling')->nullable();
            $table->integer('wooden_ceiling_unit')->nullable();
            $table->integer('wooden_ceiling_budget')->nullable();

            $table->integer('reinforced_plaster_ceiling')->nullable();
            $table->integer('reinforced_plaster_ceiling_unit')->nullable();
            $table->integer('reinforced_plaster_ceiling_budget')->nullable();

            $table->integer('applied_plaster_ceiling')->nullable();
            $table->integer('applied_plaster_ceiling_unit')->nullable();
            $table->integer('applied_plaster_ceiling_budget')->nullable();

            $table->integer('curtain_rods')->nullable();
            $table->integer('curtain_rods_unit')->nullable();
            $table->integer('curtain_rods_budget')->nullable();

            $table->integer('drawers')->nullable();
            $table->integer('drawers_unit')->nullable();
            $table->integer('drawers_budget')->nullable();

            $table->integer('bunas')->nullable();
            $table->integer('bunas_unit')->nullable();
            $table->integer('bunas_budget')->nullable();

            // Colocación de pisos / revestimientos
            $table->integer('floor_covering_total')->nullable();

            $table->integer('interior_floor_placement')->nullable();
            $table->integer('interior_floor_placement_unit')->nullable();
            $table->integer('interior_floor_placement_budget')->nullable();

            $table->integer('exterior_floor_placement')->nullable();
            $table->integer('exterior_floor_placement_unit')->nullable();
            $table->integer('exterior_floor_placement_budget')->nullable();

            $table->integer('placing_coating_in_bathrooms')->nullable();
            $table->integer('placing_coating_in_bathrooms_unit')->nullable();
            $table->integer('placing_coating_in_bathrooms_budget')->nullable();

            $table->integer('placing_coating_in_kitchens')->nullable();
            $table->integer('placing_coating_in_kitchens_unit')->nullable();
            $table->integer('placing_coating_in_kitchens_budget')->nullable();

            $table->integer('placement_on_grill')->nullable();
            $table->integer('placement_on_grill_unit')->nullable();
            $table->integer('placement_on_grill_budget')->nullable();

            $table->integer('placement_on_treadmill')->nullable();
            $table->integer('placement_on_treadmill_unit')->nullable();
            $table->integer('placement_on_treadmill_budget')->nullable();

            // Waterproofing
            $table->integer('waterproofing_final')->nullable();

            $table->integer('footbath_waterproofing')->nullable();
            $table->integer('footbath_waterproofing_unit')->nullable();
            $table->integer('footbath_waterproofing_budget')->nullable();

            $table->integer('waterproofing_terraces')->nullable();
            $table->integer('waterproofing_terraces_unit')->nullable();
            $table->integer('waterproofing_terraces_budget')->nullable();

            $table->integer('waterproofing_flower_beds')->nullable();
            $table->integer('waterproofing_flower_beds_unit')->nullable();
            $table->integer('waterproofing_flower_beds_budget')->nullable();

            // Pintura
            $table->integer('painting_total')->nullable();
            
            $table->integer('putty')->nullable();
            $table->integer('putty_unit')->nullable();
            $table->integer('putty_budget')->nullable();

            $table->integer('concrete_painting')->nullable();
            $table->integer('concrete_painting_unit')->nullable();
            $table->integer('concrete_painting_budget')->nullable();

            $table->integer('exterior_paint')->nullable();
            $table->integer('exterior_paint_unit')->nullable();
            $table->integer('exterior_paint_budget')->nullable();

            // Revoque y revestimiento exterior
            $table->integer('exterior_plaster_total')->nullable();

            $table->integer('ironing_application_exterior')->nullable();
            $table->integer('ironing_application_exterior_unit')->nullable();
            $table->integer('ironing_application_exterior_budget')->nullable();

            $table->integer('exposed_brick_wall')->nullable();
            $table->integer('exposed_brick_wall_unit')->nullable();
            $table->integer('exposed_brick_wall_budget')->nullable();

            $table->integer('stone_cladding')->nullable();
            $table->integer('stone_cladding_unit')->nullable();
            $table->integer('stone_cladding_budget')->nullable();

            $table->integer('siding_coating')->nullable();
            $table->integer('siding_coating_unit')->nullable();
            $table->integer('siding_coating_budget')->nullable();

            // Trotadora de hormingon
            $table->integer('concrete_trotter_total')->nullable();

            $table->integer('movement_vehicular_access')->nullable();
            $table->integer('movement_vehicular_access_unit')->nullable();
            $table->integer('movement_vehicular_access_budget')->nullable();

            $table->integer('placement_treadmill')->nullable();
            $table->integer('placement_treadmill_unit')->nullable();
            $table->integer('placement_treadmill_budget')->nullable();

            $table->string('concrete_trotter_extra')->nullable();

            // Administrativo
            $table->integer('administrative_total')->nullable();

            $table->integer('safety_hygiene')->nullable();
            $table->integer('safety_hygiene_unit')->nullable();
            $table->integer('safety_hygiene_budget')->nullable();

            $table->integer('personal_insurance_exterior')->nullable();
            $table->integer('personal_insurance_exterior_unit')->nullable();
            $table->integer('personal_insurance_exterior_budget')->nullable();

            $table->integer('liability_insurance')->nullable();
            $table->integer('liability_insurance_unit')->nullable();
            $table->integer('liability_insurance_budget')->nullable();

            $table->integer('third_party_insurance')->nullable();
            $table->integer('third_party_insurance_unit')->nullable();
            $table->integer('third_party_insurance_budget')->nullable();

            $table->integer('permanent_foreman_onsite')->nullable();
            $table->integer('permanent_foreman_onsite_unit')->nullable();
            $table->integer('permanent_foreman_onsite_budget')->nullable();

            $table->integer('chemical_baths')->nullable();
            $table->integer('chemical_baths_unit')->nullable();
            $table->integer('chemical_baths_budget')->nullable();

            $table->integer('containers')->nullable();
            $table->integer('containers_unit')->nullable();
            $table->integer('containers_budget')->nullable();

            // Resume
            $table->integer('guilds_administrative')->nullable();
            $table->integer('guilds')->nullable();
            $table->integer('final_budget')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
