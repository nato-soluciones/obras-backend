<?php

namespace App\Enums;

class Obra
{
  public static $paymentState = [
    'PENDING' => 'Pendiente',
    'PAID' => 'Pagado',
    'PARTIALLY_PAID' => 'Parcialmente pagado',
  ];
}
