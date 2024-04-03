<?php

namespace App\Http\Controllers;

use App\Models\ObraDailyLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ObraDailyLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, int $obraId): Response
    {
        // Recupera los oarametros de la petición
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);
        $tagId = $request->input('tag_id');
        $userId = $request->input('user_id');

        // Obtiene los registros de daily logs de la obra
        $dailyLogs = ObraDailyLog::where('obra_id', $obraId)
            ->when($tagId, function ($query, $tagId) {
                $query->where('obra_daily_log_tag_id', $tagId);
            })
            ->when($userId, function ($query, $userId) {
                $query->where('created_by_id', $userId);
            })
            ->with(['obraDailyLogTag', 'user'])
            ->orderByDesc('event_date')
            ->simplePaginate($perPage, ['*'], 'page', $page);

        return response($dailyLogs->items(), 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge(['created_by_id' => auth()->user()->id]);
        $obraDailyLog = ObraDailyLog::create($request->all());

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $directory =
                'public/uploads/dailyLogs/obra_' . $obraDailyLog->obra_id;
            $fileName = $obraDailyLog->id . '_' . $file->getClientOriginalName();
            $obraDailyLog->file_name = $fileName;

            $saveFile = $file->storeAs($directory, $fileName);

            if (!$saveFile) {
                Log::error('File upload failed (dailylogs store)');
                Log::error($file->getError());
            }

            $absolutePathToDirectory = storage_path('app/' . $directory);
            chmod($absolutePathToDirectory, 0755);
        }
        $obraDailyLog->save();


        return response($obraDailyLog, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $obraId, int $dailyLogId): Response
    {
        $obraDailyLog = ObraDailyLog::with(['obra', 'obraDailyLogTag', 'user'])->find($dailyLogId);
        return response($obraDailyLog, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $obraId, int $dailyLogId): Response
    {
        $obraDailyLog = ObraDailyLog::find($dailyLogId);
        if (!$obraDailyLog) {
            return response()->json(['error' => 'Registro no encontrado'], 404);
        }

        $requestData = $request->all();

        if ($request->hasFile('file')) {
            if ($obraDailyLog->file_name) {
                Storage::delete('public/uploads/dailyLogs/obra_' . $obraDailyLog->obra_id . '/' . $obraDailyLog->file_name);
            }

            $file = $request->file('file');
            $directory =
                'public/uploads/dailyLogs/obra_' . $obraId;
            $fileName = $requestData['id'] . '_' . $file->getClientOriginalName();
            $requestData['file_name'] = $fileName;

            $saveFile = $file->storeAs($directory, $fileName);

            if (!$saveFile) {
                Log::error('File upload failed (dailylogs store)');
                Log::error($file->getError());
            }

            $absolutePathToDirectory = storage_path('app/' . $directory);
            chmod($absolutePathToDirectory, 0755);

        } else if ($requestData['file_name'] === "null") {
            if ($obraDailyLog->file_name) {
                Storage::delete('public/uploads/dailyLogs/obra_' . $obraDailyLog->obra_id . '/' . $obraDailyLog->file_name);
            }
            $requestData['file_name'] = null;
            
        }
        
        $obraDailyLog->update($requestData);

        return response($obraDailyLog, 200);
    }

    public function fileDownload(int $obraId, int $dailyLogId)
    {
        $obraDailyLog = ObraDailyLog::find($dailyLogId);
        if (!$obraDailyLog) {
            return response()->json(['error' => 'Registro no encontrado'], 404);
        }

        $filePath = 'public/uploads/dailyLogs/obra_' . $obraDailyLog->obra_id . '/' . $obraDailyLog->file_name;
        if (!Storage::exists($filePath)) {
            return response()->json(['error' => 'Archivo no encontrado'], 404);
        }
        
        $fileName = $obraDailyLog->file_name;

        return response()->download(storage_path('app/' . $filePath), $fileName);
    }
}
