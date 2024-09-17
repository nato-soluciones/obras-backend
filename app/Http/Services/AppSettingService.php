<?php

namespace App\Http\Services;



class AppSettingService
{

  public function transformValueByType($value, $type)
  {
    switch ($type) {
      case 'int':
        return (int) $value;
      case 'boolean':
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
      case 'float':
        return (float) $value;
      case 'json':
        return json_decode($value, true);
        // Añadir más tipos si es necesario
      default:
        return $value; // Retorna como string si no hay tipo definido
    }
  }
}