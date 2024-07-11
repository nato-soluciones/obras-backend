<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

use App\Models\ManufacturerFile;
use Illuminate\Support\Facades\Log;

class ManufacturerFileController extends Controller
{
    /**
     * Create a file for manufacturer
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $data = $request->all();
        $file = $request->file('file');
        $manufacturerFile = ManufacturerFile::create($data);

        if ($file) {
            $directory = 'public/uploads/manufacturers/'.$manufacturerFile->manufacturer_id;
            $fileName = $file->getClientOriginalName();
            $filePath = Storage::putFileAs($directory, $file, $fileName, 'public');
            $manufacturerFile->file = Storage::url($filePath);

            $absolutePathToDirectory = storage_path('app/'.$directory);
            chmod($absolutePathToDirectory, 0755);
        }

        $manufacturerFile->save();

        return response($manufacturerFile, 201);
    }

    /**
     * Delete a file for manufacturer
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $manufacturerId, int $documentId): Response
    {
        $document = ManufacturerFile::find($documentId);
        if (is_null($document)) {
            return response(['status' => 404, 'message' => 'Documento no encontrado'], 404);
        }

        $directory = 'public/uploads/manufacturers/' . $manufacturerId . '/' . basename($document->file);
        if (Storage::delete($directory)) {
            $document->delete();
            return response(null, 204);
        } else {
            return response(['status' => 422, 'message' => 'No se pudo borrar el documento'], 422);
        }
    }
}
