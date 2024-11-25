<?php

namespace App\Http\Controllers;

use App\Http\Services\AppSettingService;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AppSettingController extends Controller
{
    protected $appSettingService; 

    public function __construct(AppSettingService $appSettingService)
    {
        $this->appSettingService = $appSettingService;
    }
    
    public function getSettingsByModule(string $module)
    {
        try {
            $settingsArray =  $this->appSettingService->getSettingsByModule($module);
            return response()->json($settingsArray);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al recuperar las configuraciones'], 500);
        }
    }

    public function getSettingsByKeys(Request $request)
    {
        // Validar que se envíe un array de keys
        $validated = $request->validate([
            'keys' => 'required|array',
            'keys.*' => 'string' 
        ]);

        try {
            // Recuperar los settings que coincidan con las keys proporcionadas
            $settings = AppSetting::whereIn('key', $validated['keys'])
            ->select('key', 'value', 'type')
            ->get();
    
            // Transformar los valores y estructurar la respuesta
            $settingsArray = [];
            foreach ($settings as $setting) {
                $settingsArray[$setting->key] = $this->appSettingService->transformValueByType($setting->value, $setting->type);
            }
    
            return response()->json($settingsArray);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al recuperar las configuraciones'], 500);
        }
    }
}
