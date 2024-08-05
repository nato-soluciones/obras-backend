<?php

namespace App\Enums;

class Outcome
{
  public static $types = [
    'MATERIALS' => 'Materiales',
    'CONTRACTORS' => 'Contratista',
    'PROJECT_EXPENSES' => 'Gasto de Obra',
    'OWNER_EXPENSES' => 'Gasto del Propietario',
  ];

  public static $documentTypes = [
    'BILL' => 'Factura',
    'RECEIPT' => 'Recibo',
    'DELIVERY_NOTE' => 'Remito',
  ];

  public static $paymentMethods = [
    'CASH' => 'Efectivo',
    'TRANSFER' => 'Transferencia',
    'CHECK' => 'Cheque',
    'DEPOSIT' => 'Depósito',
    'OTHER' => 'Otro',
  ];
}
