<?php

namespace App\Http\Controllers;

use App\Models\BudgetTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BudgetTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        $budgets = BudgetTemplate::all(['id', 'name']);
        return response($budgets, 200);
    }
    

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request):Response
    {
        $budgetTemplate = BudgetTemplate::create($request->all());
        return response($budgetTemplate, 201);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): Response
    {
        $budgetTemplate = BudgetTemplate::find($id);
        return response($budgetTemplate, 200);
    }



    /**
     * Update the specified resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id): Response
    {
        $budgetTemplate = BudgetTemplate::find($id);
        $budgetTemplate->update($request->all());
        return response($budgetTemplate, 200);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): Response
    {
        $budgetTemplate = BudgetTemplate::find($id);
        $budgetTemplate->delete();
        return response(['message' => 'Note deleted'], 204);
    }
}
