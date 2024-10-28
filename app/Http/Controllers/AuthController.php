<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetRequest;
use App\Http\Services\AuthService;
use Illuminate\Support\Facades\Log;

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
        try {
            $data = $request->validated();
            $this->authService->forgotPassword($data);

            return response()->json(['message' => 'Se ha enviado un correo para restablecer la contraseña!'], 200);
        } catch (\Exception $e) {
            Log::error('Error in forgotPassword: ' . $e->getMessage());
            return response()->json(['message' => 'No se ha podido enviar el correo!'], 500);
        }
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
