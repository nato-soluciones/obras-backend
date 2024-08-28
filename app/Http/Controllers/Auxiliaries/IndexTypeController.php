<?php

namespace App\Http\Controllers\Auxiliaries;

use App\Http\Controllers\Controller;
use App\Models\Auxiliaries\IndexType;
use App\Models\Cac;
use App\Models\Ipc;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class IndexTypeController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(): Response
  {
    $Banks = IndexType::select(['code', 'name'])->orderBy('name', 'asc')->get();
    return response($Banks, 200);
  }


  public function getPeriods(string $indexTypeCode): Response
  {
    //verificar si existe el tipo de indice
    $exists = IndexType::where('code', $indexTypeCode)->exists();
    if (!$exists) {
      return response(['message' => 'El tipo de indice no existe'], 201);
    }

    // Usa explode para separar el string por '_'
    $parts = explode('_', $indexTypeCode);

    $indice = $parts[0];
    $subIndice = isset($parts[1]) ? strtolower($parts[1]) : null;

    if($indice === 'IPC'){

      $ipc = Ipc::select('period', 'value')->orderBy('period', 'desc')->get();
      return response($ipc, 200);

    }else if($indice === 'CAC'){

      $cac = Cac::select('period', DB::raw("$subIndice AS value"))->orderBy('period', 'desc')->get();
      return response($cac, 200);
    }
  }

  /**
   * Display the specified resource.
   */
  // public function show(string $code): Response
  // {
  //   $bank = Bank::where('code', $code)->first();
  //   return response($bank, 200);
  // }
}
