<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

use App\Models\FleetDocument;

class FleetDocumentController extends Controller
{
    public function store(Request $request): Response
    {
        $data = $request->all();
        $file = $request->file('file');
        $document = FleetDocument::create($request->all());

        if ($file) {
            $directory = 'public/uploads/fleet_documents/'.$document->id;
            $fileName = 'file.' . $file->extension();
            $filePath = Storage::putFileAs($directory, $file, $fileName, 'public');
            $document->file = Storage::url($filePath);

            $absolutePathToDirectory = storage_path('app/'.$directory);
            chmod($absolutePathToDirectory, 0755);
        }
        $document->save();

        return response($document, 201);
    }
    
    public function destroy(int $id): Response
    {
        $document = FleetDocument::find($id);
        $document->delete();
        return response(null, 204);
    }
}
