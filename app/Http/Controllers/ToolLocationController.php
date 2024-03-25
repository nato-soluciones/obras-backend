<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

use App\Models\ToolLocation;

class ToolLocationController extends Controller
{
    /**
     * Store a new location for a tool
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $image = $request->file('image');
        $created_by = $request->user()->id;
        $data = $request->all();
        $location = ToolLocation::create(array_merge($data, ['created_by' => $created_by]));

        if ($image) {
            $directory = 'public/uploads/tools/locations/'.$location->id;
            $imageName = 'image.' . $image->extension();
            $imagePath = Storage::putFileAs($directory, $image, $imageName, 'public');
            $location->image = Storage::url($imagePath);

            $absolutePathToDirectory = storage_path('app/'.$directory);
            chmod($absolutePathToDirectory, 0755);
        }

        $location->save();

        return response($location, 201);
    }
}
