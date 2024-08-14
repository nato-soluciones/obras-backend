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
    'obraMaterials' => 'Obra Acopio',
    'obraOutcomes' => 'Obra Egresos',
    'obrasDailyLogs' => 'Diario de Obra',
    'obras' => 'Obras',
    'obraStages' => 'Etapas en avance de obra',
    'obraStageSubStages' => 'Sub-Etapas en avance de obra',
    'obraStageSubStageTasks' => 'Tareas en avance de obra',
    'obraStageSubStageTaskEvents' => 'Eventos de tareas en avance de obra',
    'providerCurrentAccountMovements' => 'Proveedor CC Movimientos',
    'providerCurrentAccounts' => 'Proveedor CC',
    'toolLocation' => 'Ubicaciones Herramientas',
    'tools' => 'Herramientas',
    'users' => 'Usuarios',

  ];

  public static $actions = [
    'list' => 'Listar',
    'display' => 'Ver',
    'insert' => 'Agregar',
    'update' => 'Modificar',
    'delete' => 'Eliminar',
    'export' => 'Exportar',
    'approve' => 'Aprobar',
    'copy' => 'Copiar',
    'review' => 'Revisar',
    'facturanteRedirect' => 'Redirigir a Facturante',
    'changeProgress' => 'Cambiar progreso',
    'listEvents' => 'Listar eventos',
    'insertEvent' => 'Agregar evento',
  ];
}
