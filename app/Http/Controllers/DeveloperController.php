<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use Illuminate\Http\Response;



class DeveloperController extends Controller
{
  /**
   * Get all clients
   *
   * @return Response
   */
  public function index(): Response
  {
    $obra = Obra::findOrFail(1);
    $resultAdditional = $obra->additionals()
      ->join('additionals_categories as ac', 'ac.additional_id', '=', 'additionals.id')
      ->join('additionals_categories_activities as aca', 'aca.additional_category_id', '=', 'ac.id')
      ->selectRaw('aca.provider_id as contractor_id, ROUND(SUM(aca.unit_cost * aca.quantity), 2) as budgeted_price')
      ->groupBy('aca.provider_id')
      ->get();
    return response($resultAdditional, 200);
  }
}
