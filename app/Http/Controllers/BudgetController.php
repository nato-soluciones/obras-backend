<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\BudgetService;
use App\Models\Budget;
use App\Models\Contractor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;


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
        $rulesValidate = [
            'date'              => 'required|date_format:Y-m-d',
            'estimated_time'    => 'required|date_format:Y-m-d',
            'obra_name'         => 'required|string|max:255',
            'covered_area'      => 'required|numeric',
            'semi_covered_area' => 'required|numeric',
            'status'            => 'required|string',
            'currency'          => 'required|string',
            'total'             => 'required|numeric',
            'total_cost'        => 'required|numeric',
            'client_id'         => 'required|numeric',
            'user_id'           => 'required|numeric',

            'categories'        => 'required|array',
            'categories.*.name' => 'required|string',
            'categories.*.total' => 'required|numeric',

            'categories.*.activities'        => 'required|array',
            'categories.*.activities.*.name' => 'required|string',
            'categories.*.activities.*.unit' => 'required|string',
            'categories.*.activities.*.unit_price' => 'required|numeric',
            'categories.*.activities.*.quantity'   => 'required|numeric',
            'categories.*.activities.*.subtotal'   => 'required|numeric',
        ];

        $messagesValidate = [
            'date.required' => 'La fecha de inicio es obligatoria.',
            'date.date_format' => 'La fecha de inicio debe estar en formato YYYY-MM-DD.',
            'estimated_time.required' => 'La fecha de finalización es obligatoria.',
            'estimated_time.date_format' => 'La fecha de finalización debe estar en formato YYYY-MM-DD.',
            'obra_name.required' => 'El nombre de la obra es obligatorio.',
            'obra_name.string' => 'El nombre de la obra debe ser un texto.',
            'obra_name.max' => 'El nombre de la obra no puede superar los 255 caracteres.',
            'covered_area.required' => 'El área cubierta es obligatoria.',
            'covered_area.numeric' => 'El área cubierta debe ser un número.',
            'semi_covered_area.required' => 'El área semicubierta es obligatoria.',
            'semi_covered_area.numeric' => 'El área semicubierta debe ser un número.',
            'status.required' => 'El estado es obligatorio.',
            'status.string' => 'El estado debe ser un texto.',
            'currency.required' => 'La moneda es obligatoria.',
            'currency.string' => 'La moneda debe ser un texto.',
            'total.required' => 'El total es obligatorio.',
            'total.numeric' => 'El total debe ser un número.',
            'total_cost.required' => 'El costo total es obligatorio.',
            'total_cost.numeric' => 'El costo total debe ser un número.',
            'client_id.required' => 'El cliente es obligatorio.',
            'client_id.numeric' => 'El cliente debe ser un número.',
            'user_id.required' => 'El responsable es obligatorio.',
            'user_id.numeric' => 'El responsable debe ser un número.',

            'categories.required' => 'Las categorías son obligatorias.',
            'categories.array' => 'Las categorías deben ser un array.',
            'categories.*.name.required' => 'El nombre de la categoría es obligatorio.',
            'categories.*.name.string' => 'El nombre de la categoría debe ser un texto.',
            'categories.*.total.required' => 'El total de la categoría es obligatorio.',
            'categories.*.total.numeric' => 'El total de la categoría debe ser un número.',

            'categories.*.activities.required' => 'Las actividades de la categoría son obligatorias.',
            'categories.*.activities.array' => 'Las actividades de la categoría deben ser un array.',
            'categories.*.activities.*.name.required' => 'El nombre de la actividad es obligatorio.',
            'categories.*.activities.*.name.string' => 'El nombre de la actividad debe ser un texto.',
            'categories.*.activities.*.unit.required' => 'La unidad de la actividad es obligatoria.',
            'categories.*.activities.*.unit.string' => 'La unidad de la actividad debe ser un texto.',
            'categories.*.activities.*.unit_price.required' => 'El precio unitario de la actividad es obligatorio.',
            'categories.*.activities.*.unit_price.numeric' => 'El precio unitario de la actividad debe ser un número.',
            'categories.*.activities.*.quantity.required' => 'La cantidad de la actividad es obligatoria.',
            'categories.*.activities.*.quantity.numeric' => 'La cantidad de la actividad debe ser un número.',
            'categories.*.activities.*.subtotal.required' => 'El subtotal de la actividad es obligatorio.',
            'categories.*.activities.*.subtotal.numeric' => 'El subtotal de la actividad debe ser un número.',
        ];

        $validator = Validator::make($request->all(), $rulesValidate, $messagesValidate);

        // Si la validación falla, devuelve los errores
        if ($validator->fails()) {
            return response($validator->errors(), 422);
        }

        // Obtiene una instancia del servicio BudgetService
        $budgetService = app(BudgetService::class);

        // Si la validación pasa, procede con la creación del presupuesto
        $budgetData = $request->all();
        $budget = $budgetService->createBudgetWithCategories($budgetData);

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
        $budget = Budget::with(['client' => function ($q) {
            $q->select('id', 'person_type', 'business_name', 'firstname', 'lastname');
        }, 'user' => function ($q) {
            $q->select('id', 'firstname', 'lastname');
        },  'categories.activities'])->find($id);

        // Carga manualmente el proveedor (contratista) para cada actividad si el campo provider_id no es nulo
        foreach ($budget->categories as $category) {
            foreach ($category->activities as $activity) {
                if ($activity->provider_id !== null) {
                    $contractorBusinessName = Contractor::where('id', $activity->provider_id)->value('business_name');
                    $activity->provider_name = $contractorBusinessName;
                }
            }
        }

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
        // Obtiene una instancia del servicio BudgetService
        $budgetService = app(BudgetService::class);

        // Si la validación pasa, procede con la actualización del presupuesto
        $budget = $budgetService->updateBudget($id, $request->all());
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
        try {
            $budget = Budget::findOrFail($id);
            $budget->delete();
            return response(['message' => 'Presupuesto eliminado correctamente'], 204);
            
        } catch (ModelNotFoundException $e) {
            return response(['error' => 'Presupuesto no encontrado'], 404);
        }
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
     * Export all budgets to CSV
     *
     * @return Response
     */
    public function exportList(): Response
    {
        $budgets = Budget::all();
        $f = fopen('php://memory', 'r+');

        $csvTitles = [
            'Presupuesto',
            'Fecha',
            'Cliente',
            'Obra',
            'Area cubierta',
            'Area semi cubierta',
            'Presupuesto Final',
            'Estado',
        ];

        fputcsv($f, $csvTitles, ',');

        foreach ($budgets as $item) {
            $csvRow = [
                $item->code,
                $item->date,
                $item->client->name,
                $item->obra_name,
                $item->covered_area,
                $item->semi_covered_area,
                $item->total,
                $item->status,
            ];
            fputcsv($f, $csvRow, ',');
        }

        rewind($f);
        $csv = stream_get_contents($f);
        fclose($f);

        return response(['datos' =>  $csv], 200);
    }
}
