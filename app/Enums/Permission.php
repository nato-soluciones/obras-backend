<?php

namespace App\Enums;

class Permission
{
  public static $entities = [
    'budgets' => 'Presupuestos',
    'calendars' => 'Calendario',
    'clientCurrentAccountMovements' => 'Cliente CC Movimientos',
    'clientCurrentAccounts' => 'Cliente CC',
    'clients' => 'Clientes',
    'companies' => 'Empresa',
    'companyCosts' => 'Costos de la Empresa',
    'contacts' => 'Agenda',
    'contractors' => 'Proveedores',
    'exchangeRates' => 'Cotizaciones',
    'fleets' => 'Vehículos',
    'indexCAC' => 'Indice CAC',
    'indexIPC' => 'Indice IPC',
    'manufacturing' => 'Productos',
    'manufacturingDocuments' => 'Documentos de Productos',
    'myTasks' => 'Mis tareas',
    'navbar' => 'Menú lateral',
    'notes' => 'Notas',
    'obraAdditional' => 'Obra Adicionales',
    'obraContractors' => 'Obra Proveedores',
    'obraDocuments' => 'Obra Documentos',
    'obraIncomes' => 'Obra Ingresos',
    'obraMenu' => 'Menu de Obra',
    'obraMaterials' => 'Obra Acopio',
    'obraOutcomes' => 'Obra Egresos',
    'obraPlanChargeDetails' => 'Obra Plan de Cobro Cuotas',
    'obraPlanCharges' => 'Obra Plan de Cobros',
    'obras' => 'Obras',
    'obrasDailyLogs' => 'Diario de Obra',
    'obraStages' => 'Etapas en avance de obra',
    'obraStageSubStages' => 'Sub-Etapas en avance de obra',
    'obraStageSubStageTaskEvents' => 'Eventos de tareas en avance de obra',
    'obraStageSubStageTasks' => 'Tareas en avance de obra',
    'providerCurrentAccountMovements' => 'Proveedor CC Movimientos',
    'providerCurrentAccounts' => 'Proveedor CC',
    'toolLocation' => 'Ubicaciones Herramientas',
    'tools' => 'Herramientas',
    'users' => 'Usuarios',
  ];

  public static $actions = [
    'approve' => 'Aprobar',
    'changeProgress' => 'Cambiar progreso',
    'charge' => 'Cobrar',
    'copy' => 'Copiar',
    'delete' => 'Eliminar',
    'display' => 'Ver',
    'export' => 'Exportar',
    'facturanteRedirect' => 'Redirigir a Facturante',
    'insert' => 'Agregar',
    'insertEvent' => 'Agregar evento',
    'list' => 'Listar',
    'listEvents' => 'Listar eventos',
    'review' => 'Revisar',
    'update' => 'Modificar',
  ];

  public static $obraMenu = [
    'overview' => 'Vista general',
    'stages' => 'Avance de obra',
    'dailyLogs' => 'Diario de obra',
    'materials' => 'Acopio',
    'incomes' => 'Ingresos',
    'planCharge' => 'Plan de cobros',
    'outcomes' => 'Egresos',
    'additionals' => 'Adicionales',
    'providers' => 'Proveedores',
    'documents' => 'Documentos',
    'settings' => 'Configuraciones',
  ];
}
