<?php

namespace App\Http\Controllers;

use App\Http\Services\AppSettingService;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppSettingController extends Controller
{
    protected $appSettingService; 

    public function __construct(AppSettingService $appSettingService)
    {
        $this->appSettingService = $appSettingService;
    }
    
    public function getSettingsByModule(string $module)
    {
        $settings = AppSetting::where('module', $module)
            ->select("key", "value", "type")
            ->get();

        if (!$settings) {
            return response()->json(["message" => "El modulo no existe"], 404);
        }

        $settingsArray = [];
        foreach ($settings as $setting) {
            Log::debug($setting);
            $settingsArray[$setting->key] = $this->appSettingService->transformValueByType($setting->value, $setting->type);
        }

        return response()->json($settingsArray);
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
