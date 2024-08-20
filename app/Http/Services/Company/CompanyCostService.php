<?php

namespace App\Http\Services\Company;

use App\Models\Company\CompanyCost;
use Illuminate\Http\Request;

class CompanyCostService
{

  public function list(Request $request)
  {
    $period = $request->input('period');
    $category_id = $request->input('category');
    $payment_status = $request->input('paymentStatus');

    $companyCost = CompanyCost::with('category', 'responsible')
      ->when($period, function ($query, $period) {
        $query->where('period', $period);
      })
      ->when($category_id, function ($query, $category_id) {
        $query->where('category_id', $category_id);
      })
      ->when($payment_status, function ($query, $payment_status) {
        $query->where('payment_status', $payment_status);
      })
      ->orderBy('registration_date', 'desc')
      ->paginate(20);

    $companyCostData = [];
    foreach ($companyCost as $cost) {
      $companyCostData[] = [
        'id' => $cost->id,
        'registration_date' => $cost->registration_date,
        'period' => $cost->period,
        'description' => $cost->description,
        'amount' => $cost->amount,
        'payment_status' => $cost->payment_status,
        'payment_date' => $cost->payment_date,
        'category_id' => $cost->category?->id,
        'category_name' => $cost->category?->name,
        'responsible_id' => $cost->responsible?->id,
        'responsible_name' => $cost->responsible?->firstname . ' ' . $cost->responsible?->lastname,
      ];
    }

    $response = [
      'data' => $companyCostData,
      'current_page' => $companyCost->currentPage(),
      'last_page' => $companyCost->lastPage(),
    ];

    return $response;
  }
}
