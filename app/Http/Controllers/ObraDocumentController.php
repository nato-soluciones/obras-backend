<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ObraDocumentController extends Controller
{
    public function index(Request $request, int $obraId): Response
    {
        $documents = Document::where('obra_id', $obraId)->get();
        return response($documents, 200);
    }

    /**
     * Store a document for an obra
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function store(Request $request, int $obraId): Response
    {
        $name = $request->input('name');
        $category = $request->input('category');
        $document = $request->file('file');

        $directory = 'public/uploads/obras/' . $obraId;
        $documentName = $document->getClientOriginalName();
        $documentPath = Storage::putFileAs($directory, $document, $documentName, 'public');

        Document::create([
            'name' => $name,
            'category' => $category,
            'path' => Storage::url($documentPath),
            'obra_id' => $obraId
        ]);

        $absolutePathToDirectory = storage_path('app/' . $directory);
        chmod($absolutePathToDirectory, 0755);

        return response(['message' => 'Document uploaded'], 201);
    }

    /**
     * Delete a document for an obra
     *
     * @param int $id
     * @param int $documentId
     * @return Response
     */
    public function destroy(int $obraId, int $documentId): Response
    {
        $document = Document::find($documentId);
        if (is_null($document)) {
            return response(['status' => 404, 'message' => 'Documento no encontrado'], 404);
        }
        $directory = 'public/uploads/obras/'.$obraId.'/'.basename($document->path);
        if (Storage::delete($directory)) {
            $document->delete();
            return response(null, 204);
        } else {
            return response(['status' => 422, 'message' => 'No se pudo borrar el documento'], 422);
        }
    }
}
