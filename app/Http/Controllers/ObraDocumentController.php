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
        $documents = Document::from('obra_documents as od')
            ->join('obra_documents_categories as odc', 'od.category_id', '=', 'odc.id')
            ->select('od.id', 'od.name', 'od.storage_type', 'od.link', 'od.path', 'od.category_id', 'odc.name as category_name')
            ->where('od.obra_id', $obraId)
            ->orderBy('od.category_id', 'asc')
            ->orderBy('od.name', 'asc')
            ->get();
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
        $data = $request->all();
        $data['obra_id'] = $obraId;
        $file = $request->file('file');

        $document = Document::create($data);

        if ($file) {
            $directory = 'public/uploads/obras/' . $obraId;
            $fileName = $file->getClientOriginalName();
            $filePath = Storage::putFileAs($directory, $file, $fileName, 'public');
            $document->path = Storage::url($filePath);

            $absolutePathToDirectory = storage_path('app/' . $directory);
            chmod($absolutePathToDirectory, 0755);
        }

        $document->save();

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
        if ($document->storage_type === 'link') {
            $document->delete();
            return response(null, 204);
            
        } else if ($document->storage_type === 'file') {
            $directory = 'public/uploads/obras/' . $obraId . '/' . basename($document->path);
            if (Storage::delete($directory)) {
                $document->delete();
                return response(null, 204);
            } else {
                return response(['status' => 422, 'message' => 'No se pudo borrar el documento'], 422);
            }
        }
    }
}
