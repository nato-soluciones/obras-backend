<?php

namespace Database\Seeders;

use App\Models\RoleRelationship;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RelationalRolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ? RELACIÓN DE USUARIOS CON ROLES DE USUARIO 
        $users = ["tomasgimenez11@gmail.com", "superadmin@gmail.com", "Arquitecto@gmail.com"];
        foreach ($users as $user) {
            $userRol = User::where('email', $user)->first();
            if ($userRol) {
                if ($userRol['email'] === "superadmin@gmail.com") $userRol->syncRoles("SUPERADMIN");
                if ($userRol['email'] === "Arquitecto@gmail.com") $userRol->syncRoles("ARCHITECT");
                if ($userRol['email'] === "tomasgimenez11@gmail.com") $userRol->syncRoles("OWNER");
            }
        }


        // ? RELACIÓN DE ROLES FUNCIONALES CON PERMISOS 
        $navbar_permissions = [
            "navbar_obras",
            "navbar_contractors",
            "navbar_clients",
            "navbar_budgets",
            "navbar_tools",
            "navbar_manufacturing",
            "navbar_indices",
            "navbar_users",
            "navbar_contacts",
            "navbar_calendar",
            "navbar_exchange_rate",
            "navbar_notes",
            "navbar_fleets"
        ];
        $functionalRol = Role::where('name', 'functional_navbar_full')->first();
        $functionalRol->givePermissionTo($navbar_permissions);

        $obras_permission_names = [
            "obras_list",
            "obras_insert",
            "obras_update",
            "obras_delete",
            "obras_display"
        ];
        $functionalRol = Role::where('name', 'functional_obras_full')->first();
        $functionalRol->givePermissionTo($obras_permission_names);

        $obrasDailyLogs_permission_names = [
            "obrasDailyLogs_list",
            "obrasDailyLogs_insert",
            "obrasDailyLogs_update",
            "obrasDailyLogs_delete"
        ];
        $functionalRol = Role::where('name', 'functional_obrasDailyLogs_full')->first();
        $functionalRol->givePermissionTo($obrasDailyLogs_permission_names);

        $obraStages_permission_names = [
            "obraStages_list",
            "obraStages_insert",
            "obraStages_update",
            "obraStages_delete",
            "obraStages_display"
        ];
        $functionalRol = Role::where('name', 'functional_obraStages_full')->first();
        $functionalRol->givePermissionTo($obraStages_permission_names);

        $obras_stage_tasks_permission_names = [
            "obraStageTasks_list",
            "obraStageTasks_insert",
            "obraStageTasks_update",
            "obraStageTasks_delete",
            "obraStageTasks_display"
        ];
        $functionalRol = Role::where('name', 'functional_obraStageTasks_full')->first();
        $functionalRol->givePermissionTo($obras_stage_tasks_permission_names);

        $obras_incomes_permission_names = [
            "obraIncomes_list",
            "obraIncomes_insert",
            "obraIncomes_update",
            "obraIncomes_delete",
            "obraIncomes_display",
            "obraIncomes_export"
        ];
        $functionalRol = Role::where('name', 'functional_obraIncomes_full')->first();
        $functionalRol->givePermissionTo($obras_incomes_permission_names);

        $obras_outcomes_permission_names = [
            "obraOutcomes_list",
            "obraOutcomes_insert",
            "obraOutcomes_update",
            "obraOutcomes_delete",
            "obraOutcomes_display",
            "obraOutcomes_export",
            "obraOutcomes_facturanteRedirect"
        ];
        $functionalRol = Role::where('name', 'functional_obraOutcomes_full')->first();
        $functionalRol->givePermissionTo($obras_outcomes_permission_names);
        
        $obras_materials_permission_names = [
            "obraMaterials_list",
            "obraMaterials_insert",
            "obraMaterials_update",
            "obraMaterials_delete",
            "obraMaterials_display"
        ];
        $functionalRol = Role::where('name', 'functional_obraMaterials_full')->first();
        $functionalRol->givePermissionTo($obras_materials_permission_names);

        $obras_additionals_permission_names = [
            "obraAdditional_list",
            "obraAdditional_insert",
            "obraAdditional_update",
            "obraAdditional_delete",
            "obraAdditional_display"
        ];
        $functionalRol = Role::where('name', 'functional_obraAdditionals_full')->first();
        $functionalRol->givePermissionTo($obras_additionals_permission_names);

        $obras_contractors_permission_names = [
            "obraContractors_list",
            "obraContractors_export"
        ];
        $functionalRol = Role::where('name', 'functional_obraContractors_full')->first();
        $functionalRol->givePermissionTo($obras_contractors_permission_names);

        $obras_documents_permission_names = [
            "obraDocuments_list",
            "obraDocuments_insert",
            "obraDocuments_update",
            "obraDocuments_delete"
        ];
        $functionalRol = Role::where('name', 'functional_obraDocuments_full')->first();
        $functionalRol->givePermissionTo($obras_documents_permission_names);

        $contractors_permission_names = [
            "contractors_list",
            "contractors_insert",
            "contractors_update",
            "contractors_delete",
            "contractors_display",
            "contractors_export"
        ];
        $functionalRol = Role::where('name', 'functional_contractors_full')->first();
        $functionalRol->givePermissionTo($contractors_permission_names);

        $provider_current_accounts_permission_names = [
            "providerCurrentAccounts_list",
            "providerCurrentAccounts_insert",
            "providerCurrentAccounts_display",
        ];
        $functionalRol = Role::where('name', 'functional_providerCurrentAccounts_full')->first();
        $functionalRol->givePermissionTo($provider_current_accounts_permission_names);

        $provider_current_accounts_movements_permission_names = [
            "providerCurrentAccountMovements_list",
            "providerCurrentAccountMovements_insert",
            "providerCurrentAccountMovements_update",
            "providerCurrentAccountMovements_display",
        ];
        $functionalRol = Role::where('name', 'functional_providerCurrentAccountMovements_full')->first();
        $functionalRol->givePermissionTo($provider_current_accounts_movements_permission_names);

        $budgets_permission_names = [
            "budgets_list",
            "budgets_insert",
            "budgets_update",
            "budgets_delete",
            "budgets_display",
            "budgets_export",
            "budgets_copy",
            "budgets_approve",
            "budgets_review"
        ];
        $functionalRol = Role::where('name', 'functional_budgets_full')->first();
        $functionalRol->givePermissionTo($budgets_permission_names);

        $clients_permission_names = [
            "clients_list",
            "clients_insert",
            "clients_update",
            "clients_delete",
            "clients_display"
        ];
        $functionalRol = Role::where('name', 'functional_clients_full')->first();
        $functionalRol->givePermissionTo($clients_permission_names);

        $client_current_accounts_permission_names = [
            "clientCurrentAccounts_list",
            "clientCurrentAccounts_insert",
            "clientCurrentAccounts_display",
        ];
        $functionalRol = Role::where('name', 'functional_clientCurrentAccounts_full')->first();
        $functionalRol->givePermissionTo($client_current_accounts_permission_names);

        $client_current_accounts_movements_permission_names = [
            "clientCurrentAccountMovements_list",
            "clientCurrentAccountMovements_insert",
            "clientCurrentAccountMovements_update",
            "clientCurrentAccountMovements_display",
        ];
        $functionalRol = Role::where('name', 'functional_clientCurrentAccountMovements_full')->first();
        $functionalRol->givePermissionTo($client_current_accounts_movements_permission_names);

        $tools_permission_names = [
            "tools_list",
            "tools_insert",
            "tools_update",
            "tools_delete",
            "tools_display"
        ];
        $functionalRol = Role::where('name', 'functional_tools_full')->first();
        $functionalRol->givePermissionTo($tools_permission_names);

        $tool_locations_permission_names = [
            "toolLocation_list",
            "toolLocation_insert",
            "toolLocation_update",
            "toolLocation_delete",
            "toolLocation_display"
        ];
        $functionalRol = Role::where('name', 'functional_toolLocations_full')->first();
        $functionalRol->givePermissionTo($tool_locations_permission_names);

        $manufacturing_permission_names = [
            "manufacturing_list",
            "manufacturing_insert",
            "manufacturing_update",
            "manufacturing_delete",
            "manufacturing_display"
        ];
        $functionalRol = Role::where('name', 'functional_manufacturing_full')->first();
        $functionalRol->givePermissionTo($manufacturing_permission_names);

        $manufacturing_document_permission_names = [
            "manufacturingDocuments_list",
            "manufacturingDocuments_insert",
            "manufacturingDocuments_delete",
            "manufacturingDocuments_display"
        ];
        $functionalRol = Role::where('name', 'functional_manufacturingDocuments_full')->first();
        $functionalRol->givePermissionTo($manufacturing_document_permission_names);

        $index_ipc_permission_names = [
            "indexIPC_list",
            "indexIPC_insert",
            "indexIPC_update",
            "indexIPC_delete",
            "indexIPC_display"
        ];
        $functionalRol = Role::where('name', 'functional_indexIPC_full')->first();
        $functionalRol->givePermissionTo($index_ipc_permission_names);

        $index_cac_permission_names = [
            "indexCAC_list",
            "indexCAC_insert",
            "indexCAC_update",
            "indexCAC_delete",
            "indexCAC_display"
        ];
        $functionalRol = Role::where('name', 'functional_indexCAC_full')->first();
        $functionalRol->givePermissionTo($index_cac_permission_names);

        $users_permission_names = [
            "users_list",
            "users_insert",
            "users_update",
            "users_delete",
            "users_display"
        ];
        $functionalRol = Role::where('name', 'functional_users_full')->first();
        $functionalRol->givePermissionTo($users_permission_names);

        $contacts_permission_names = [
            "contacts_list",
            "contacts_insert",
            "contacts_update",
            "contacts_delete",
            "contacts_display"
        ];
        $functionalRol = Role::where('name', 'functional_contacts_full')->first();
        $functionalRol->givePermissionTo($contacts_permission_names);

        $calendars_permissions_names = [
            "calendars_list"
        ];
        $functionalRol = Role::where('name', 'functional_calendar_full')->first();
        $functionalRol->givePermissionTo($calendars_permissions_names);

        $exchangeRates_permissions_names = [
            "exchangeRates_list"
        ];
        $functionalRol = Role::where('name', 'functional_exchangeRates_full')->first();
        $functionalRol->givePermissionTo($exchangeRates_permissions_names);

        $notes_permission_names = [
            "notes_list",
            "notes_insert",
            "notes_update",
            "notes_delete",
            "notes_display"
        ];
        $functionalRol = Role::where('name', 'functional_notes_full')->first();
        $functionalRol->givePermissionTo($notes_permission_names);

        $fleets_permission_names = [
            "fleets_list",
            "fleets_insert",
            "fleets_update",
            "fleets_delete",
            "fleets_display"
        ];
        $functionalRol = Role::where('name', 'functional_fleets_full')->first();
        $functionalRol->givePermissionTo($fleets_permission_names);

        // ? RELACIÓN DE ROL DE USUARIO (DUEÑO) CON ROLES FUNCIONALES
        $userRol = Role::where('name', 'OWNER')->first();
        $functionalRoles_names = [
            "functional_navbar_full",
            "functional_obras_full",
            "functional_obrasDailyLogs_full",
            "functional_obraStages_full",
            "functional_obraStageTasks_full",
            "functional_obraIncomes_full",
            "functional_obraOutcomes_full",
            "functional_obraMaterials_full",
            "functional_obraAdditionals_full",
            "functional_obraContractors_full",
            "functional_obraDocuments_full",
            "functional_contractors_full",
            "functional_providerCurrentAccounts_full",
            "functional_providerCurrentAccountMovements_full",
            "functional_budgets_full",
            "functional_clients_full",
            "functional_clientCurrentAccounts_full",
            "functional_clientCurrentAccountMovements_full",
            "functional_tools_full",
            "functional_toolLocations_full",
            "functional_manufacturing_full",
            "functional_manufacturingDocuments_full",
            "functional_indexIPC_full",
            "functional_indexCAC_full",
            "functional_users_full",
            "functional_contacts_full",
            "functional_calendar_full",
            "functional_exchangeRates_full",
            "functional_notes_full",
            "functional_fleets_full"
        ];

        $roleFunctionals = Role::whereIn('name', $functionalRoles_names)->get();
        foreach ($roleFunctionals as $roleFunctional) {
            if ($roleFunctional->permissions->count() > 0) {
                RoleRelationship::FirstOrCreate([
                    'functional_role_id' => $roleFunctional['id'],
                    'user_role_id' => $userRol['id']
                ]);

                $userRol->givePermissionTo($roleFunctional->permissions()->pluck('id'));
            }
        }
    }
}
