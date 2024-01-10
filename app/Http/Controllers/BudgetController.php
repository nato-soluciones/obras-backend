<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use App\Models\Budget;

class BudgetController extends Controller
{
    /**
     * Get all budgets
     *
     * @return Response
     */
    public function index(): Response
    {
        $budgets = Budget::with(['client', 'user'])->get();
        return response($budgets, 200);
    }

    /**
     * Create an budget
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {

        $data = $request->all();
        $data['user_id'] = Auth::user()->id;
        $budget = Budget::create($data);
        return response($budget, 201);
    }

    /**
     * Get an budget by id
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $budget = Budget::find($id);
        return response($budget, 200);
    }

    /**
     * Update an budget by id
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        $budget = Budget::find($id);
        $budget->update($request->all());
        return response($budget, 200);
    }

    /**
     * Delete an budget by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $budget = Budget::find($id);
        $budget->delete();
        return response(['message' => 'Budget deleted'], 200);
    }

    /**
     * Approve an budget by id
     *
     * @param int $id
     * @return Response
     */
    public function approve(int $id): Response
    {
        $budget = Budget::find($id);
        $budget->status = 'APPROVED';
        $budget->save();
        return response(['message' => 'Budget approved', 'data' => $budget], 200);
    }

    /**
     * Revert an approved budget by id
     *
     * @param int $id
     * @return Response
     */
    public function revert(int $id): Response
    {
        $budget = Budget::find($id);
        $budget->status = 'PENDING';
        $budget->save();
        return response(['message' => 'Budget reverted', 'data' => $budget], 200);
    }

    /**
     * Finish an approved budget by id
     *
     * @param int $id
     * @return Response
     */
    public function finish(int $id): Response
    {
        $budget = Budget::find($id);
        $budget->status = 'FINISHED';
        $budget->save();
        return response(['message' => 'Budget finished', 'data' => $budget], 200);
    }
}
