<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;

use App\Models\Income;
use App\Notifications\IncomeCreated;

class IncomeController extends Controller
{

    /** 
     * Get all income list
     *
     * @return Response
     */
    public function index(): Response
    {
        $incomes = Income::with('obra.client')->get();
        return response($incomes, 200);
    }

    /**
     * Create an income
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $income = Income::create($request->all());
        Notification::route('mail', $income->email)
                    ->notify(new IncomeCreated($income));        

        return response($income, 201);
    }

    /**
     * Get an income
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $income = Income::with('obra.client')->find($id);
        if (is_null($income)) {
            return response()->json(['message' => 'Income not found'], 404);
        }
        return response($income, 200);
    }

    /**
     * Update an income
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        $income = Income::find($id);
        if (is_null($income)) {
            return response()->json(['message' => 'Income not found'], 404);
        }
        $income->update($request->all());
        return response($income, 200);
    }

    /**
     * Delete an income
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $income = Income::find($id);
        if (is_null($income)) {
            return response()->json(['message' => 'Income not found'], 404);
        }
        $income->delete();
        return response()->json(['message' => 'Income deleted'], 204);
    }
}