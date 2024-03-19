<?php

namespace App\Http\Controllers;

use App\Models\ContractorIndustry;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ContractorIndustryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $contractorIndustries = ContractorIndustry::all(['code', 'name']);
        return response($contractorIndustries, 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $code): Response
    {
        $contractorIndustry = ContractorIndustry::where('code', $code)->first();
        return response($contractorIndustry, 200);
    }

}
