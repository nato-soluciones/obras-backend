<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Http\Resources\Material\MaterialCollectionResource;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use App\Models\StoreMovementMaterial;
use App\Services\Material\MaterialService;

class MaterialController extends Controller
{

    public function __construct(
        private MaterialService $materialService,
    ) {}
        
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     try {
    //         $filters = $request->only(['dateFrom', 'dateTo', 'status','type', 'orderBy', 'direction', 'search']);
    //         $trips = $this->tripService->getListTrips($filters);
    //         return response()->json([
    //             'data' => TripCollectionResource::collection($trips)->response()->getData(true)['data'],
    //             'current_page' => $trips->currentPage(),
    //             'last_page' => $trips->lastPage(),
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error($e->getMessage());
    //         return response()->json(['message' => 'Error al obtener los viajes'], 500);
    //     }
    // }
    public function index(Request $request)
    {
        try{
            $filters = $request->only(['categoryId', 'color', 'orderBy', 'direction', 'search']);
            $materials = $this->materialService->getListMaterials($filters);

            // return response()->json([
            //     'data' => MaterialCollectionResource::collection($materials)->response()->getData(true)['data'],
            //     'current_page' => $materials->currentPage(),
            //     'last_page' => $materials->lastPage(),
            // ]);
            return response()->json(
                MaterialCollectionResource::collection($materials),
            );

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al obtener los materiales'], 500);
        }
        // $materials = Material::with(['measurementUnit', 'storeMaterials'])
        //     ->orderBy('name', 'asc')
        //     ->get()
        //     ->map(function ($material) {
        //         // Calcular stock total sumando todos los store_materials
        //         $totalStock = $material->storeMaterials->sum('quantity');

        //         // Buscar el último movimiento que involucre este material
        //         $lastMovement = StoreMovementMaterial::where('material_id', $material->id)
        //             ->latest('created_at')
        //             ->first();

        //         return [
        //             'id' => $material->id,
        //             'name' => $material->name,
        //             'code' => $material->code,
        //             'category' => $material->category,
        //             'dimensions' => $material->dimensions,
        //             'quantity_per_package' => $material->quantity_per_package,
        //             'color' => $material->color,
        //             'description' => $material->description,
        //             'unit' => $material->measurementUnit->name,
        //             'unit_abbreviation' => $material->measurementUnit->abbreviation,
        //             'stock' => $totalStock,
        //             'lastMovement' => $lastMovement ? $lastMovement->created_at->format('d/m/Y') : null
        //         ];
        //     });

        return response($materials, 200);
    }

    /**
     * Create a manufacturer category
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $name = strtolower($request->input('name'));
        $code = $request->input('code');
        $color = $request->input('color');

        // Verificar si ya existe un material con el mismo nombre, codigo y color, (sin importar mayúsculas/minúsculas)
        $exists = Material::whereRaw('LOWER(name) = ?', [$name])->where('code', $code)->where('color', $color)->exists();

        if ($exists) {
            return response(['message' => 'Ya existe un material con este nombre, codigo y color'], 201);
        }
        $material = Material::create($request->all());
        return response($material, 201);
    }

    /**
     * Get a manufacturer category by id
     *
     * @param int $id
     * @return Response
     */
    public function show(string $id): Response
    {
        $material = Material::with('measurementUnit')->findOrFail($id);
        
        $formatted = [
            'id' => $material->id,
            'name' => $material->name,
            'code' => $material->code,
            'category' => $material->category,
            'dimensions' => $material->dimensions,
            'quantity_per_package' => $material->quantity_per_package,
            'color' => $material->color,
            'description' => $material->description,
            'measurement_unit' => $material->measurementUnit
        ];
        
        return response($formatted, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, string $id)
    {
        try {
            $material = Material::findOrFail($id);
            $material->update($request->all());
            return response($material, 200);
        } catch (ModelNotFoundException $e) {
            return response(['error' => 'Material no encontrado'], 404);
        }
    }

    /**
     * Delete a manufacturer category by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $material = Material::find($id);
        $material->delete();
        return response(null, 204);
    }

    public function getStoresByMaterial(int $id): Response
    {
        $material = Material::with(['measurementUnit', 'storeMaterials.store'])->findOrFail($id);

        $totalStock = $material->storeMaterials->sum('quantity');

        $storesWithStock = $material->storeMaterials->map(function ($storeMaterial) {
            return [
                'store_id' => $storeMaterial->store?->id,
                'store_name' => $storeMaterial->store?->name,
                'quantity' => $storeMaterial->quantity,
                'minimum_limit' => $storeMaterial->minimum_limit,
                'critical_limit' => $storeMaterial->critical_limit,
            ];
        });

        $formattedResponse = [
            'material' => [
                'id' => $material->id,
                'name' => $material->name,
                'code' => $material->code,
                'category' => $material->category,
                'dimensions' => $material->dimensions,
                'quantity_per_package' => $material->quantity_per_package,
                'color' => $material->color,
                'description' => $material->description,
                'total_stock' => $totalStock,
                'measurement_unit' => [
                    'id' => $material->measurementUnit?->id,
                    'name' => $material->measurementUnit?->name,
                    'abbreviation' => $material->measurementUnit?->abbreviation
                ]
            ],
            'stores' => $storesWithStock
        ];

        return response($formattedResponse, 200);
    }
}
