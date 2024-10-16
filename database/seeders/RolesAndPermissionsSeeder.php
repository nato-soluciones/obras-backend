<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ? Crea Roles de Usuario, si no existen
        $userRoles = [
            [
                "name" => "SUPERADMIN",
                "guard_name" => "api",
                "description" => "Super Administrador",
            ],
            [
                "name" => "OWNER",
                "guard_name" => "api",
                "description" => "Dueño",
            ],
            [
                "name" => "ARCHITECT",
                "guard_name" => "api",
                "description" => "Arquitecto",
            ],
            [
                "name" => "CONSTRUCTION_MANAGER",
                "guard_name" => "api",
                "description" => "Jefe de Obra",
            ],
            [
                "name" => "ADMINISTRATIVE",
                "guard_name" => "api",
                "description" => "Administrativo",
            ],
            [
                "name" => "CLIENT",
                "guard_name" => "api",
                "description" => "Cliente",
            ]
        ];

        foreach ($userRoles as $role) {
            $existingRole = Role::firstOrNew(
                ['name' => $role['name'], 'guard_name' => $role['guard_name']],
                ['description' => $role['description']]
            );

            if ($existingRole->exists) {
                // Si el rol ya existe, actualiza la descripción
                $existingRole->description = $role['description'];
                $existingRole->save();
            } else {
                // Si el rol no existe, créalo
                $existingRole->save();
            }
        }

        // ? Crea Permisos, si no existen
        $navbar_permissions = [
            [
                "name" => "navbar_obras",
                "guard_name" => "api",
                "description" => "Menú Obras",
            ],
            [
                "name" => "navbar_myTasks",
                "guard_name" => "api",
                "description" => "Menú Mis Tareas",
            ],
            [
                "name" => "navbar_contractors",
                "guard_name" => "api",
                "description" => "Menú Contratistas",
            ],
            [
                "name" => "navbar_clients",
                "guard_name" => "api",
                "description" => "Menú Clientes",
            ],
            [
                "name" => "navbar_budgets",
                "guard_name" => "api",
                "description" => "Menú Presupuestos",
            ],
            [
                "name" => "navbar_tools",
                "guard_name" => "api",
                "description" => "Menú Herramientas",
            ],
            [
                "name" => "navbar_manufacturing",
                "guard_name" => "api",
                "description" => "Menú Fabricación",
            ],
            [
                "name" => "navbar_indices",
                "guard_name" => "api",
                "description" => "Menú Índices",
            ],
            [
                "name" => "navbar_users",
                "guard_name" => "api",
                "description" => "Menú Usuarios",
            ],
            [
                "name" => "navbar_contacts",
                "guard_name" => "api",
                "description" => "Menú Agenda",
            ],
            [
                "name" => "navbar_exchangeRates",
                "guard_name" => "api",
                "description" => "Menú Cotizaciones",
            ],
            [
                "name" => "navbar_notes",
                "guard_name" => "api",
                "description" => "Menú Notas",
            ],
            [
                "name" => "navbar_fleets",
                "guard_name" => "api",
                "description" => "Menú Flotas",
            ],
            [
                "name" => "navbar_companies",
                "guard_name" => "api",
                "description" => "Menú Empresas",
            ]
        ];
        $obras_permission = [
            [
                "name" => "obras_list",
                "guard_name" => "api",
                "description" => "Ver Obras",
            ],
            [
                "name" => "obras_insert",
                "guard_name" => "api",
                "description" => "Agregar obra",
            ],
            [
                "name" => "obras_update",
                "guard_name" => "api",
                "description" => "Modificar obra",
            ],
            [
                "name" => "obras_delete",
                "guard_name" => "api",
                "description" => "Eliminar obra",
            ],
            [
                "name" => "obras_display",
                "guard_name" => "api",
                "description" => "Ver Contratista",
            ],
        ];
        $obras_dailyLog_permission = [
            [
                "name" => "obrasDailyLogs_list",
                "guard_name" => "api",
                "description" => "Ver diario de obra",
            ],
            [
                "name" => "obrasDailyLogs_insert",
                "guard_name" => "api",
                "description" => "Agregar diario de obra",
            ],
            [
                "name" => "obrasDailyLogs_update",
                "guard_name" => "api",
                "description" => "Modificar diario de obra",
            ],
            [
                "name" => "obrasDailyLogs_delete",
                "guard_name" => "api",
                "description" => "Eliminar diario de obra",
            ],
        ];
        $obras_stages_permission = [
            [
                "name" => "obraStages_list",
                "guard_name" => "api",
                "description" => "Ver Etapas",
            ],
            [
                "name" => "obraStages_insert",
                "guard_name" => "api",
                "description" => "Agregar etapa",
            ],
            [
                "name" => "obraStages_update",
                "guard_name" => "api",
                "description" => "Modificar etapa",
            ],
            [
                "name" => "obraStages_delete",
                "guard_name" => "api",
                "description" => "Eliminar etapa",
            ],
            [
                "name" => "obraStages_display",
                "guard_name" => "api",
                "description" => "Ver etapa",
            ],
        ];
        $obras_stage_subStages_permission = [
            [
                "name" => "obraStageSubStages_list",
                "guard_name" => "api",
                "description" => "Ver Sub-Etapas de la etapa",
            ],
            [
                "name" => "obraStageSubStages_insert",
                "guard_name" => "api",
                "description" => "Agregar sub-etapa",
            ],
            [
                "name" => "obraStageSubStages_update",
                "guard_name" => "api",
                "description" => "Modificar sub-etapa",
            ],
            [
                "name" => "obraStageSubStages_delete",
                "guard_name" => "api",
                "description" => "Eliminar sub-etapa",
            ],
            [
                "name" => "obraStageSubStages_display",
                "guard_name" => "api",
                "description" => "Ver sub-etapa",
            ],
        ];
        $obras_stage_subStage_tasks_permission = [
            [
                "name" => "obraStageSubStageTasks_list",
                "guard_name" => "api",
                "description" => "Ver Tareas de la etapa",
            ],
            [
                "name" => "obraStageSubStageTasks_insert",
                "guard_name" => "api",
                "description" => "Agregar tarea",
            ],
            [
                "name" => "obraStageSubStageTasks_update",
                "guard_name" => "api",
                "description" => "Modificar tarea",
            ],
            [
                "name" => "obraStageSubStageTasks_delete",
                "guard_name" => "api",
                "description" => "Eliminar tarea",
            ],
            [
                "name" => "obraStageSubStageTasks_display",
                "guard_name" => "api",
                "description" => "Ver tarea",
            ],
            [
                "name" => "obraStageSubStageTasks_changeProgress",
                "guard_name" => "api",
                "description" => "Actualizar avance de tarea",
            ],
            [
                "name" => "obraStageSubStageTasks_listEvents",
                "guard_name" => "api",
                "description" => "Ver los eventos de la tarea",
            ],
        ];
        $obras_incomes_permission = [
            [
                "name" => "obraIncomes_list",
                "guard_name" => "api",
                "description" => "Ver Ingresos de la obra",
            ],
            [
                "name" => "obraIncomes_insert",
                "guard_name" => "api",
                "description" => "Agregar Ingreso de la obra",
            ],
            [
                "name" => "obraIncomes_update",
                "guard_name" => "api",
                "description" => "Modificar Ingreso de la obra",
            ],
            [
                "name" => "obraIncomes_delete",
                "guard_name" => "api",
                "description" => "Eliminar Ingreso de la obra",
            ],
            [
                "name" => "obraIncomes_display",
                "guard_name" => "api",
                "description" => "Ver Ingreso de la obra",
            ],
            [
                "name" => "obraIncomes_export",
                "guard_name" => "api",
                "description" => "Exportar Ingresos de la obra",
            ],
            [
                "name" => "obraIncomes_facturanteRedirect",
                "guard_name" => "api",
                "description" => "Redirecciona a facturante",
            ],
        ];
        $obras_outcomes_permission = [
            [
                "name" => "obraOutcomes_list",
                "guard_name" => "api",
                "description" => "Ver Egresos de la obra",
            ],
            [
                "name" => "obraOutcomes_insert",
                "guard_name" => "api",
                "description" => "Agregar egreso de la obra",
            ],
            [
                "name" => "obraOutcomes_update",
                "guard_name" => "api",
                "description" => "Modificar egreso de la obra",
            ],
            [
                "name" => "obraOutcomes_delete",
                "guard_name" => "api",
                "description" => "Eliminar egreso de la obra",
            ],
            [
                "name" => "obraOutcomes_display",
                "guard_name" => "api",
                "description" => "Ver egreso de la obra",
            ],
            [
                "name" => "obraOutcomes_export",
                "guard_name" => "api",
                "description" => "Exportar egresos de la obra",
            ],
        ];
        $obras_additional_permission = [
            [
                "name" => "obraAdditional_list",
                "guard_name" => "api",
                "description" => "Ver adicionales de la obra",
            ],
            [
                "name" => "obraAdditional_insert",
                "guard_name" => "api",
                "description" => "Agregar adicional de la obra",
            ],
            [
                "name" => "obraAdditional_update",
                "guard_name" => "api",
                "description" => "Modificar adicional de la obra",
            ],
            [
                "name" => "obraAdditional_delete",
                "guard_name" => "api",
                "description" => "Eliminar adicional de la obra",
            ],
            [
                "name" => "obraAdditional_display",
                "guard_name" => "api",
                "description" => "Ver adicional de la obra",
            ],
        ];
        $obras_contractors_permission = [
            [
                "name" => "obraContractors_list",
                "guard_name" => "api",
                "description" => "Ver Contratistas de la obra",
            ],
            [
                "name" => "obraContractors_export",
                "guard_name" => "api",
                "description" => "Exportar Contratistas de la obra",
            ],
        ];
        $obras_documents_permission = [
            [
                "name" => "obraDocuments_list",
                "guard_name" => "api",
                "description" => "Ver Documentos de la obra",
            ],
            [
                "name" => "obraDocuments_insert",
                "guard_name" => "api",
                "description" => "Agregar documento de la obra",
            ],
            [
                "name" => "obraDocuments_update",
                "guard_name" => "api",
                "description" => "Modificar documento de la obra",
            ],
            [
                "name" => "obraDocuments_delete",
                "guard_name" => "api",
                "description" => "Eliminar documento de la obra",
            ],
        ];
        $obras_materials_permission = [
            [
                "name" => "obraMaterials_list",
                "guard_name" => "api",
                "description" => "Ver materiales de la obra",
            ],
            [
                "name" => "obraMaterials_insert",
                "guard_name" => "api",
                "description" => "Agregar material de la obra",
            ],
            [
                "name" => "obraMaterials_update",
                "guard_name" => "api",
                "description" => "Modificar material de la obra",
            ],
            [
                "name" => "obraMaterials_delete",
                "guard_name" => "api",
                "description" => "Eliminar material de la obra",
            ],
            [
                "name" => "obraMaterials_display",
                "guard_name" => "api",
                "description" => "Ver material de la obra",
            ],
        ];
        $my_tasks_permission = [
            [
                "name" => "myTasks_list",
                "guard_name" => "api",
                "description" => "Ver mis tareas de la obra",
            ],
            [
                "name" => "myTasks_changeProgress",
                "guard_name" => "api",
                "description" => "Actualizar avance de tarea",
            ],
            [
                "name" => "myTasks_listEvents",
                "guard_name" => "api",
                "description" => "Lista los eventos de mi tarea",
            ],
            [
                "name" => "myTasks_insertEvent",
                "guard_name" => "api",
                "description" => "Agregar Evento a mi tarea",
            ],
        ];
        $contractors_permission = [
            [
                "name" => "contractors_list",
                "guard_name" => "api",
                "description" => "Ver Contratistas",
            ],
            [
                "name" => "contractors_insert",
                "guard_name" => "api",
                "description" => "Agregar Contratista",
            ],
            [
                "name" => "contractors_update",
                "guard_name" => "api",
                "description" => "Modificar Contratista",
            ],
            [
                "name" => "contractors_delete",
                "guard_name" => "api",
                "description" => "Eliminar Contratista",
            ],
            [
                "name" => "contractors_display",
                "guard_name" => "api",
                "description" => "Ver Contratista",
            ],
            [
                "name" => "contractors_export",
                "guard_name" => "api",
                "description" => "Exportar Contratistas",
            ],
        ];
        $provider_current_accounts_permission = [
            [
                "name" => "providerCurrentAccounts_list",
                "guard_name" => "api",
                "description" => "Listar Cuentas corrientes de proveedores",
            ],
            [
                "name" => "providerCurrentAccounts_insert",
                "guard_name" => "api",
                "description" => "Agregar Cuenta corriente de proveedor",
            ],
            [
                "name" => "providerCurrentAccounts_display",
                "guard_name" => "api",
                "description" => "Ver Cuenta corriente de proveedores",
            ],
        ];
        $provider_current_accounts_movements_permission = [
            [
                "name" => "providerCurrentAccountMovements_list",
                "guard_name" => "api",
                "description" => "Listar Movimientos de cuentas corrientes en proveedores",
            ],
            [
                "name" => "providerCurrentAccountMovements_insert",
                "guard_name" => "api",
                "description" => "Agregar Movimiento de cuenta corriente en proveedores",
            ],
            [
                "name" => "providerCurrentAccountMovements_update",
                "guard_name" => "api",
                "description" => "Modificar Movimiento de cuenta corriente en proveedores",
            ],
            [
                "name" => "providerCurrentAccountMovements_display",
                "guard_name" => "api",
                "description" => "Ver Movimiento de cuenta corriente en proveedores",
            ],
        ];
        $budgets_permission = [
            [
                "name" => "budgets_list",
                "guard_name" => "api",
                "description" => "Ver Presupuestos",
            ],
            [
                "name" => "budgets_insert",
                "guard_name" => "api",
                "description" => "Agregar Presupuesto",
            ],
            [
                "name" => "budgets_update",
                "guard_name" => "api",
                "description" => "Modificar Presupuesto",
            ],
            [
                "name" => "budgets_delete",
                "guard_name" => "api",
                "description" => "Eliminar Presupuesto",
            ],
            [
                "name" => "budgets_display",
                "guard_name" => "api",
                "description" => "Ver Presupuesto",
            ],
            [
                "name" => "budgets_export",
                "guard_name" => "api",
                "description" => "Exportar Presupuestos",
            ],
            [
                "name" => "budgets_copy",
                "guard_name" => "api",
                "description" => "Copiar Presupuesto",
            ],
            [
                "name" => "budgets_approve",
                "guard_name" => "api",
                "description" => "Aprobar Presupuesto",
            ],
            [
                "name" => "budgets_review",
                "guard_name" => "api",
                "description" => "Revisar presupuesto para aprobación",
            ],
        ];
        $clients_permission = [
            [
                "name" => "clients_list",
                "guard_name" => "api",
                "description" => "Ver Clientes",
            ],
            [
                "name" => "clients_insert",
                "guard_name" => "api",
                "description" => "Agregar Cliente",
            ],
            [
                "name" => "clients_update",
                "guard_name" => "api",
                "description" => "Modificar Cliente",
            ],
            [
                "name" => "clients_delete",
                "guard_name" => "api",
                "description" => "Eliminar Cliente",
            ],
            [
                "name" => "clients_display",
                "guard_name" => "api",
                "description" => "Ver Cliente",
            ],
        ];
        $client_current_accounts_permission = [
            [
                "name" => "clientCurrentAccounts_list",
                "guard_name" => "api",
                "description" => "Listar Cuentas corrientes de clientes",
            ],
            [
                "name" => "clientCurrentAccounts_insert",
                "guard_name" => "api",
                "description" => "Agregar Cuenta corriente de cliente",
            ],
            [
                "name" => "clientCurrentAccounts_display",
                "guard_name" => "api",
                "description" => "Ver Cuenta corriente de cliente",
            ],
        ];
        $client_current_accounts_movements_permission = [
            [
                "name" => "clientCurrentAccountMovements_list",
                "guard_name" => "api",
                "description" => "Listar Movimientos de cuentas corrientes en clientes",
            ],
            [
                "name" => "clientCurrentAccountMovements_insert",
                "guard_name" => "api",
                "description" => "Agregar Movimiento de cuenta corriente en clientes",
            ],
            [
                "name" => "clientCurrentAccountMovements_update",
                "guard_name" => "api",
                "description" => "Modificar Movimiento de cuenta corriente en clientes",
            ],
            [
                "name" => "clientCurrentAccountMovements_display",
                "guard_name" => "api",
                "description" => "Ver Movimiento de cuenta corriente en clientes",
            ],
        ];
        $tools_permission = [
            [
                "name" => "tools_list",
                "guard_name" => "api",
                "description" => "Ver Herramientas",
            ],
            [
                "name" => "tools_insert",
                "guard_name" => "api",
                "description" => "Agregar Herramienta",
            ],
            [
                "name" => "tools_update",
                "guard_name" => "api",
                "description" => "Modificar Herramienta",
            ],
            [
                "name" => "tools_delete",
                "guard_name" => "api",
                "description" => "Eliminar Herramienta",
            ],
            [
                "name" => "tools_display",
                "guard_name" => "api",
                "description" => "Ver Herramienta",
            ],
        ];
        $tool_locations_permission = [
            [
                "name" => "toolLocation_list",
                "guard_name" => "api",
                "description" => "Ver Ubicaciones de la herramienta",
            ],
            [
                "name" => "toolLocation_insert",
                "guard_name" => "api",
                "description" => "Agregar ubicación de la herramienta",
            ],
            [
                "name" => "toolLocation_update",
                "guard_name" => "api",
                "description" => "Modificar ubicación de la herramienta",
            ],
            [
                "name" => "toolLocation_delete",
                "guard_name" => "api",
                "description" => "Eliminar ubicación de la herramienta",
            ],
            [
                "name" => "toolLocation_display",
                "guard_name" => "api",
                "description" => "Ver ubicación de la herramienta",
            ],
        ];
        $manufacturing_permission = [
            [
                "name" => "manufacturing_list",
                "guard_name" => "api",
                "description" => "Ver Productos",
            ],
            [
                "name" => "manufacturing_insert",
                "guard_name" => "api",
                "description" => "Agregar Producto",
            ],
            [
                "name" => "manufacturing_update",
                "guard_name" => "api",
                "description" => "Modificar Producto",
            ],
            [
                "name" => "manufacturing_delete",
                "guard_name" => "api",
                "description" => "Eliminar Producto",
            ],
            [
                "name" => "manufacturing_display",
                "guard_name" => "api",
                "description" => "Ver Producto",
            ],
        ];
        $manufacturing_document_permission = [
            [
                "name" => "manufacturingDocuments_list",
                "guard_name" => "api",
                "description" => "Ver documentos del productos",
            ],
            [
                "name" => "manufacturingDocuments_insert",
                "guard_name" => "api",
                "description" => "Agregar documento al producto",
            ],
            [
                "name" => "manufacturingDocuments_delete",
                "guard_name" => "api",
                "description" => "Eliminar documento del producto",
            ],
            [
                "name" => "manufacturingDocuments_display",
                "guard_name" => "api",
                "description" => "Ver documento del producto",
            ],
        ];
        $index_ipc_permission = [
            [
                "name" => "indexIPC_list",
                "guard_name" => "api",
                "description" => "Ver Indices IPC",
            ],
            [
                "name" => "indexIPC_insert",
                "guard_name" => "api",
                "description" => "Agregar Indice IPC",
            ],
            [
                "name" => "indexIPC_update",
                "guard_name" => "api",
                "description" => "Modificar Indice IPC",
            ],
            [
                "name" => "indexIPC_delete",
                "guard_name" => "api",
                "description" => "Eliminar Indice IPC",
            ],
            [
                "name" => "indexIPC_display",
                "guard_name" => "api",
                "description" => "Ver Indice IPC",
            ],
        ];
        $index_cac_permission = [
            [
                "name" => "indexCAC_list",
                "guard_name" => "api",
                "description" => "Ver Indices CAC",
            ],
            [
                "name" => "indexCAC_insert",
                "guard_name" => "api",
                "description" => "Agregar Indice CAC",
            ],
            [
                "name" => "indexCAC_update",
                "guard_name" => "api",
                "description" => "Modificar Indice CAC",
            ],
            [
                "name" => "indexCAC_delete",
                "guard_name" => "api",
                "description" => "Eliminar Indice CAC",
            ],
            [
                "name" => "indexCAC_display",
                "guard_name" => "api",
                "description" => "Ver Indice CAC",
            ],
        ];
        $users_permission = [
            [
                "name" => "users_list",
                "guard_name" => "api",
                "description" => "Ver Usuarios",
            ],
            [
                "name" => "users_insert",
                "guard_name" => "api",
                "description" => "Agregar Usuario",
            ],
            [
                "name" => "users_update",
                "guard_name" => "api",
                "description" => "Modificar Usuario",
            ],
            [
                "name" => "users_delete",
                "guard_name" => "api",
                "description" => "Eliminar Usuario",
            ],
            [
                "name" => "users_display",
                "guard_name" => "api",
                "description" => "Ver Usuario",
            ],
            [
                "name" => "users_role_change",
                "guard_name" => "api",
                "description" => "Cambiar Rol de Usuario",
            ],
        ];
        $contacts_permission = [
            [
                "name" => "contacts_list",
                "guard_name" => "api",
                "description" => "Ver Agenda de contactos",
            ],
            [
                "name" => "contacts_insert",
                "guard_name" => "api",
                "description" => "Agregar Contacto",
            ],
            [
                "name" => "contacts_update",
                "guard_name" => "api",
                "description" => "Modificar Contacto",
            ],
            [
                "name" => "contacts_delete",
                "guard_name" => "api",
                "description" => "Eliminar Contacto",
            ],
            [
                "name" => "contacts_display",
                "guard_name" => "api",
                "description" => "Ver Contacto",
            ],
        ];
        $exchangeRates_permissions = [
            [
                "name" => "exchangeRates_list",
                "guard_name" => "api",
                "description" => "Ver Cotizaciones",
            ],
        ];
        $notes_permission = [
            [
                "name" => "notes_list",
                "guard_name" => "api",
                "description" => "Ver Notas",
            ],
            [
                "name" => "notes_insert",
                "guard_name" => "api",
                "description" => "Agregar Nota",
            ],
            [
                "name" => "notes_update",
                "guard_name" => "api",
                "description" => "Modificar Nota",
            ],
            [
                "name" => "notes_delete",
                "guard_name" => "api",
                "description" => "Eliminar Nota",
            ],
            [
                "name" => "notes_display",
                "guard_name" => "api",
                "description" => "Ver Nota",
            ],
        ];
        $fleets_permission = [
            [
                "name" => "fleets_list",
                "guard_name" => "api",
                "description" => "Ver Flotas",
            ],
            [
                "name" => "fleets_insert",
                "guard_name" => "api",
                "description" => "Agregar Flota",
            ],
            [
                "name" => "fleets_update",
                "guard_name" => "api",
                "description" => "Modificar Flota",
            ],
            [
                "name" => "fleets_delete",
                "guard_name" => "api",
                "description" => "Eliminar Flota",
            ],
            [
                "name" => "fleets_display",
                "guard_name" => "api",
                "description" => "Ver Flota",
            ],
        ];
        $company_costs_permission = [
            [
                "name" => "companyCosts_list",
                "guard_name" => "api",
                "description" => "Ver Costos de la Empresa",
            ],
            [
                "name" => "companyCosts_insert",
                "guard_name" => "api",
                "description" => "Agregar Costo",
            ],
            [
                "name" => "companyCosts_update",
                "guard_name" => "api",
                "description" => "Modificar Costo",
            ],
            [
                "name" => "companyCosts_display",
                "guard_name" => "api",
                "description" => "Ver Costo",
            ],
            [
                "name" => "companyCosts_delete",
                "guard_name" => "api",
                "description" => "Eliminar Costo",
            ],
        ];
        $obra_plan_charges_permission = [
            [
                "name" => "obraPlanCharges_insert",
                "guard_name" => "api",
                "description" => "Agregar Plan de cobros",
            ],
        ];
        $obra_plan_charge_details_permission = [
            [
                "name" => "obraPlanChargeDetails_list",
                "guard_name" => "api",
                "description" => "Ver detalle del plan de cobros",
            ],
            [
                "name" => "obraPlanChargeDetails_insert",
                "guard_name" => "api",
                "description" => "Agregar detalle al plan de cobros",
            ],
            [
                "name" => "obraPlanChargeDetails_charge",
                "guard_name" => "api",
                "description" => "Cobrar detalle del plan de cobros",
            ],
            [
                "name" => "obraPlanChargeDetails_display",
                "guard_name" => "api",
                "description" => "Ver detalle del plan de cobros",
            ]
        ];

        $permissions = array_merge(
            $navbar_permissions,
            $obras_permission,
            $obras_dailyLog_permission,
            $obras_stages_permission,
            $obras_stage_subStages_permission,
            $obras_stage_subStage_tasks_permission,
            $obras_incomes_permission,
            $obras_outcomes_permission,
            $obras_additional_permission,
            $obras_contractors_permission,
            $obras_documents_permission,
            $obras_materials_permission,
            $my_tasks_permission,
            $contractors_permission,
            $provider_current_accounts_permission,
            $provider_current_accounts_movements_permission,
            $budgets_permission,
            $clients_permission,
            $client_current_accounts_permission,
            $client_current_accounts_movements_permission,
            $tools_permission,
            $tool_locations_permission,
            $manufacturing_permission,
            $manufacturing_document_permission,
            $index_ipc_permission,
            $index_cac_permission,
            $users_permission,
            $contacts_permission,
            $exchangeRates_permissions,
            $notes_permission,
            $fleets_permission,
            $company_costs_permission,
            $obra_plan_charges_permission,
            $obra_plan_charge_details_permission
        );

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }
    }
}
