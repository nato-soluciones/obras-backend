<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $Banks = Bank::select(['code', 'name', 'is_virtual_wallet'])->orderBy('name', 'asc')->get();
        return response($Banks, 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $code): Response
    {
        $bank = Bank::where('code', $code)->first();
        return response($bank, 200);
    }
}
