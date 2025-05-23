<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Tool;
use App\Http\Requests\CreateToolRequest;

class ToolController extends Controller
{
    /**
     * Get all tools
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        $perPage = 20;
        $status = $request->input('status', 'ALL');
        $query = Tool::with(['category', 'last_location'])
            ->orderBy('name', 'asc');
        if ($status !== 'ALL') {
            $query->where('status', $status);
        }
        $tools = $query->paginate($perPage);

        $response = [
            'data' => $tools->items(),
            'current_page' => $tools->currentPage(),
            'last_page' => $tools->lastPage(),
            'total' => $tools->total(),
        ];
        return response($response, 200);
    }

    /**
     * Create an tool
     *
     * @param Request $request
     * @return Response
     */
    public function store(CreateToolRequest $request): Response
    {
        $data = $request->all();
        $image = $request->file('image');
        $tool = Tool::create($request->all());

        if ($image) {
            $directory = 'public/uploads/tools/' . $tool->id;
            $imageName = 'image.' . $image->extension();
            $imagePath = Storage::putFileAs($directory, $image, $imageName, 'public');
            $tool->image = Storage::url($imagePath);

            $absolutePathToDirectory = storage_path('app/' . $directory);
            chmod($absolutePathToDirectory, 0755);
        }
        $tool->save();

        return response($tool, 201);
    }

    /**
     * Get an tool by id
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $tool = Tool::with(['locations' => function ($query) {
            $query->orderBy('date', 'desc');
        }])->find($id);
        return response($tool, 200);
    }

    /**
     * Update an tool by id
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        $tool = Tool::find($id);

        $new_image = $request->file('new_image');
        $tool->update($request->all());

        if ($new_image) {
            $directory = 'public/uploads/tools/' . $tool->id;
            $imageName = 'image.' . $new_image->extension();
            $imagePath = Storage::putFileAs($directory, $new_image, $imageName, 'public');
            $tool->image = Storage::url($imagePath);

            $absolutePathToDirectory = storage_path('app/' . $directory);
            chmod($absolutePathToDirectory, 0755);
        }
        $tool->save();

        return response($tool, 200);
    }

    /**
     * Delete an tool by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $tool = Tool::find($id);
        if (is_null($tool)) {
            return response(['status' => 404, 'message' => 'Herramienta no encontrada'], 404);
        }
        $directory = 'public/uploads/tools/' . $id;

        if (Storage::deleteDirectory($directory)) {
            $tool->delete();
            return response(null, 204);
        } else {
            return response(['status' => 422, 'message' => 'No se pudo borrar la herramienta'], 422);
        }

    }
}
