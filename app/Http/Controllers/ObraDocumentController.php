<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
    public function destroy(int $id, int $documentId): Response
    {
        $document = Document::find($documentId);
        $document->delete();

        $absolutePathToFile = storage_path('app/' . $document->path);
        unlink($absolutePathToFile);

        return response(['message' => 'Document deleted'], 204);
    }
}
