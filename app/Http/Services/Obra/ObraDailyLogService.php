<?php

namespace App\Http\Services\Obra;

use App\Models\ObraDailyLog;
use App\Models\ObraDailyLogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ObraDailyLogService
{
  public function store(Request $request)
  {
    try {
      $request->merge(['created_by_id' => auth()->user()->id]);
      $obraDailyLog = ObraDailyLog::create($request->all());

      $obraDailyLog->file_name = null;
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
    } catch (\Exception $e) {
      Log::error("Error al crear el diario de obra {$request->name}: " . $e->getMessage());
      throw ValidationException::withMessages(['obra' => "Error al crear el diario de obra {$request->name}"]);
    }

    return response($obraDailyLog, 201);
  }

  public function getDailyLogTagByName(string $name)
  {
    return ObraDailyLogTag::where('name', $name)->first();
  }
}
