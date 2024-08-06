<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetRequest;
use App\Http\Services\AuthService;

class AuthController extends Controller
{
    /**
     * Dependency injection
     */
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Login
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $accessToken = $this->authService->login($credentials);

        return $accessToken
            ? response($accessToken)
            : response(['message' => 'El usuario o la contraseña son incorrectos.'], 401);
    }

    /**
     * Logout
     */
    public function logout()
    {
        $success = $this->authService->logout();

        return $success
            ? response()->json(['message' => 'Cierre de sesión con éxito!'], 200)
            : response(['message' => 'Se ha producido un error al cerrar la sesión del usuario.'], 500);
    }

    /**
     * Forgot Password
     */
    public function forgotPassword(ForgotRequest $request)
    {
        $data = $request->validated();
        $success = $this->authService->forgotPassword($data);

        return $success
            ? response(['message' => 'Successfully sent password reset email!'], 200)
            : response(['message' => 'There was a problem sending the email!'], 500);
    }

    /**
     * Reset Password
     */
    public function resetPassword(ResetRequest $request)
    {
        $data = $request->validated();
        $success = $this->authService->resetPassword($data);

        return $success
            ? response(['message' => 'Successfully reset of password!'], 200)
            : response(['message' => 'There was a problem reseting the password!'], 500);
    }
}
