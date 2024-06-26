<?php

namespace App\Http\Controllers;

use App\Models\MeasurementUnit;
use Illuminate\Http\Request;

class MeasurementUnitController extends Controller
{

    public function index()
    {
        $measurementUnits = MeasurementUnit::orderBy('name')->get();
        return response($measurementUnits, 200);
    }   
}
