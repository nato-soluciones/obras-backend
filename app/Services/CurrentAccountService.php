<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Contractor;
use App\Models\CurrentAccount;
use App\Models\CurrentAccountMovement;
use App\Models\CurrentAccountMovementType;
use Illuminate\Support\Facades\Log;

class CurrentAccountService
{
  public function CurrentAccountGet(string $entity_type, int $entity_id, int $projectId, string $currency)
  {
    //? Validaciones //
    // Entity type habilitada
    if (!in_array($entity_type, CurrentAccount::ENTITY_TYPES)) {
      return ['status' => 422, 'message'  => 'Entidad (' . $entity_type . ') no aceptada'];
    }
    if (!is_numeric($entity_id)) {
      return ['status' => 422, 'message' => 'El código de ' . ($entity_type === 'CLIENT' ? 'cliente' : 'proveedor') . ' debe ser numérico'];
    }
    // Currency habilitada
    if (!in_array($currency, CurrentAccount::CURRENCIES)) {
      return ['status' => 422, 'message'  => 'Moneda (' . $currency . ') no aceptada'];
    }

    // Existencia del cliente o proveedor
    if ($entity_type === 'CLIENT') {
      $client = Client::find($entity_id);
      if (!$client) {
        return ['status' => 422, 'message'  => 'El cliente no existe'];
      }
    } else if ($entity_type === 'PROVIDER') {
      $provider = Contractor::find($entity_id);
      if (!$provider) {
        return ['status' => 422, 'message'  => 'El proveedor / contratista no existe'];
      }
    }

    $currentAccount = CurrentAccount::firstOrCreate(
      ['entity_type' => $entity_type, 'entity_id' => $entity_id, 'project_id' => $projectId, 'currency' => $currency],
      ['balance' => 0]
    );

    return ['status' => 200, 'currentAccount' => $currentAccount];
  }

  public function CAMovementList(string $entityType, int $entityId, int $projectId, string $currency, int $page = 0, int $per_page = 20)
  {
    $ca_movements = CurrentAccountMovement::select(
      'id',
      'current_account_id',
      'date',
      'movement_type_id',
      'description',
      'amount',
      'observation'
    )
      ->whereHas('currentAccount', function ($query) use ($entityType, $entityId, $projectId, $currency) {
        $query->where('entity_type', $entityType)
          ->where('entity_id', $entityId)
          ->where('project_id', $projectId)
          ->where('currency', $currency);
      })
      ->with('movementType:id,name,type')
      ->orderBy('date', 'desc')
      ->orderBy('id', 'desc')
      ->get();

    $currentBalance = 0;
    $movementsWithBalance = [];

    $cantMovements = count($ca_movements) - 1;
    for ($i = $cantMovements; $i >= 0; $i--) {
      $movement = $ca_movements[$i];
      $movementType = $movement->movementType->type;
      $amount = $movement->amount;

      if ($movementType === 'DEBIT') {
        $currentBalance += $amount;
      } else {
        $currentBalance -= $amount;
      }

      $movementWithBalance = $movement;
      $movementWithBalance['current_balance'] = $currentBalance;
      array_unshift($movementsWithBalance, $movementWithBalance);
    }

    return $movementsWithBalance;
  }

  public function CAMovementAdd($CAData, $CAMovementData)
  {
    //? VALIDACIONES //
    // Existencia del tipo de movimiento
    $movementType = CurrentAccountMovementType::find($CAMovementData['movement_type_id']);
    if (!$movementType) {
      return ['status' => 422, 'message'  => 'El tipo de movimiento (' . $CAMovementData['movement_type_id'] . ') no existe'];
    }


    // Obtiene la cuenta corriente
    $RespCurrentAccount = $this->CurrentAccountGet($CAData['entity_type'], $CAData['entity_id'], $CAData['project_id'], $CAData['currency']);

    if ($RespCurrentAccount['status'] !== 200) {
      return $RespCurrentAccount;
    }
    $currentAccount = $RespCurrentAccount['currentAccount'];

    $newBalance = ($movementType['type'] === 'DEBIT'
      ? $currentAccount->balance + $CAMovementData['amount']
      : $currentAccount->balance - $CAMovementData['amount']);

    // Crea el movimiento de la cuenta corriente
    $CAMovementData['current_account_id'] = $currentAccount->id;

    // Log::info("CAData");
    // Log::info($CAData);
    // Log::info("Movimiento ADD");
    // Log::info($CAMovementData);

    // Agrega el movimiento a la cuenta corriente
    $currentAccount->movements()->create($CAMovementData);

    // Actualiza el saldo de la cuenta corriente
    $currentAccount->balance = $newBalance;
    $currentAccount->save();

    return ['status' => 200, 'message' => 'Movimiento creado ok'];
  }

  public function CAMovementUpdateByReference($CAData, $CAMovementData)
  {

    // Obtiene la cuenta corriente
    $RespCurrentAccount = $this->CurrentAccountGet($CAData['entity_type'], $CAData['entity_id'], $CAData['project_id'], $CAData['currency']);

    if ($RespCurrentAccount['status'] !== 200) {
      return $RespCurrentAccount;
    }
    $currentAccount = $RespCurrentAccount['currentAccount'];

    // Recuperar el movimiento de referencia
    $referenceEntity = $CAMovementData['reference_entity'];
    $referenceId = $CAMovementData['reference_id'];
    $referenceMovement = CurrentAccountMovement::where('current_account_id', $currentAccount->id)
      ->where('reference_entity', $referenceEntity)
      ->where('reference_id', $referenceId)
      ->first();

    if (!$referenceMovement) {
      return ['status' => 422, 'message'  => 'El movimiento de referencia (' . $CAMovementData['reference_id'] . ') no existe.'];
    }

    // Log::info("UPD - CAData");
    // Log::info($currentAccount);
    // Log::info("Movimiento DB UPD");
    // Log::info($referenceMovement);
    // Log::info("Movimiento NEW UPD");
    // Log::info($CAMovementData);
    if (floatval($referenceMovement->amount) !== floatval($CAMovementData['amount'])) {
      
      $newBalance = ($referenceMovement->movementType->type === 'DEBIT'
      ? $currentAccount->balance + $referenceMovement->amount + $CAMovementData['amount']
      : $currentAccount->balance - $referenceMovement->amount - $CAMovementData['amount']);
      
      
      // Log::info("newBalance");
      // Log::info($newBalance);

      // Actualiza el movimiento de la cuenta corriente
      $CAMovementData['description'] = $referenceMovement->description;
      $referenceMovement->update($CAMovementData);
      // Actualiza el saldo de la cuenta corriente
      $currentAccount->balance = $newBalance;
      $currentAccount->save();
    }

    return ['status' => 200, 'message' => 'Movimiento modificado ok'];
  }
  public function CAMovementDeleteByReference($CAData, $CAMovementData)
  {

    // Obtiene la cuenta corriente
    $RespCurrentAccount = $this->CurrentAccountGet($CAData['entity_type'], $CAData['entity_id'], $CAData['project_id'], $CAData['currency']);

    if ($RespCurrentAccount['status'] !== 200) {
      return $RespCurrentAccount;
    }
    $currentAccount = $RespCurrentAccount['currentAccount'];

    // Recupera el movimiento de referencia
    $referenceEntity = $CAMovementData['reference_entity'];
    $referenceId = $CAMovementData['reference_id'];
    $referenceMovement = CurrentAccountMovement::where('current_account_id', $currentAccount->id)
      ->where('reference_entity', $referenceEntity)
      ->where('reference_id', $referenceId)
      ->first();

    if (!$referenceMovement) {
      return ['status' => 422, 'message'  => 'El movimiento de referencia (' . $CAMovementData['reference_id'] . ') no existe.'];
    }

    $movementType = CurrentAccountMovementType::find($CAMovementData['movement_type_id']);
    if (!$movementType) {
      return ['status' => 422, 'message'  => 'El tipo de movimiento (' . $CAMovementData['movement_type_id'] . ') no existe.'];
    }

    $newBalance = ($movementType->type === 'DEBIT'
      ? $currentAccount->balance - $referenceMovement->amount
      : $currentAccount->balance + $referenceMovement->amount);

    // Log::info("DLT - CAData");
    // Log::info($currentAccount);
    // Log::info("Movimiento DB DLT");
    // Log::info($referenceMovement);
    // Log::info("Movimiento NEW DLT");
    // Log::info($CAMovementData);
    // Log::info("newBalance");
    // Log::info($newBalance);

    // Actualiza el movimiento de la cuenta corriente
    $referenceMovement->delete();
    // Actualiza el saldo de la cuenta corriente
    $currentAccount->balance = $newBalance;
    $currentAccount->save();


    return ['status' => 200, 'message' => 'Movimiento eliminado ok'];
  }
}
