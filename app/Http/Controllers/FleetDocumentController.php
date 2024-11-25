<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

use App\Models\FleetDocument;

class FleetDocumentController extends Controller
{
    public function store(Request $request, int $fleetId): Response
    {
        $file = $request->file('file');
        $document = FleetDocument::create($request->all());

        if ($file) {
            $directory = 'public/uploads/fleets/' . $fleetId . '/documents/' . $document->id;
            $fileName = 'file.' . $file->extension();
            $filePath = Storage::putFileAs($directory, $file, $fileName, 'public');
            $document->file = Storage::url($filePath);

            $absolutePathToDirectory = storage_path('app/' . $directory);
            chmod($absolutePathToDirectory, 0755);
        }
        $document->save();

        return response($document, 201);
    }

    public function destroy(int $fleetId, int $documentId): Response
    {
        $document = FleetDocument::find($documentId);
        if (is_null($document)) {
            return response(['status' => 404, 'message' => 'Documento no encontrado'], 404);
        }

        $directory = 'public/uploads/fleets/' . $fleetId . '/documents/' . $documentId;
        if (Storage::deleteDirectory($directory)) {
            $document->delete();
            return response(null, 204);
        } else {
            return response(['status' => 422, 'message' => 'No se pudo borrar el documento'], 422);
        }
    }
}
