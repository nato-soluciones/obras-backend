<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

use App\Models\ManufacturerFile;

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
    public function destroy(int $id): Response
    {
        $manufacturerFile = ManufacturerFile::find($id);
        $manufacturerFile->delete();

        return response(['message' => 'Manufacturer deleted'], 204);
    }
}
