<?php

namespace App\Http\Controllers;

use App\Http\Services\AppSettingService;
use App\Http\Services\User\UserService;

class InitialSettingController extends Controller
{

    private $appSettingService;
    private $userService;

    public function __construct(AppSettingService $appSettingService, UserService $userService) {
        $this->appSettingService = $appSettingService;
        $this->userService = $userService;
    }

    public function index()
    {
        $settingsArray =  $this->appSettingService->getSettingsByModule('WEATHER');
        $permissions = $this->userService->entityCheck('navbar');

        // Si no hay configuraciones, se envían las configuraciones por defecto
        if(count($settingsArray) === 0 || empty($settingsArray['LOCATION_URL'])) {
            $settingsArray = [
                'LOCATION_NAME' => 'MAR DEL PLATA',
                'LOCATION_URL' => 'https://forecast7.com/es/n38d01n57d54/mar-del-plata/',
            ];
        }

        $response = [
            'permissions' => $permissions,
            'weather' => $settingsArray
        ];
        return response($response, 200);
    }
}
