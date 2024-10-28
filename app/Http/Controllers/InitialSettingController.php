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

        $response = [
            'permissions' => $permissions,
            'weather' => $settingsArray
        ];
        return response($response, 200);
    }
}
