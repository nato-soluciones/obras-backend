<?php

namespace App\Http\Services;


use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{

  /**
   * Login service
   *
   * @return String accessToken
   */
  public function login($credentials)
  {

    if (Auth::attempt($credentials)) {
      /** @var Illuminate\Foundation\Auth\User $user */
      $user = Auth::user();
      $token = $user->createToken('Access Token')->plainTextToken;

      return [
        'access_token' => $token,
        'user' => $user
      ];
    }

    return null;
  }

  /**
   * Forgot Password
   *
   * @return Boolean success
   */
  public function forgotPassword(array $data)
  {
    $status = Password::sendResetLink(['email' => $data['email']]);

    if ($status != Password::RESET_LINK_SENT) {
      throw ValidationException::withMessages([
        'email' => [__($status)],
      ]);
    }

    return true;
  }

  /**
   * Reset Password
   *
   * @return Boolean success
   */
  public function resetPassword($data)
  {
    $status = Password::reset(
      $data,
      function ($user, $password) {
        $user->forceFill(['password' => Hash::make($password)])->setRememberToken(Str::random(60));
        $user->save();

        event(new PasswordReset($user));
      }
    );

    return $status === Password::PASSWORD_RESET;
  }

  /**
   * Logout service
   *
   * @return Boolean success
   */
  public function logout()
  {
    /** @var Illuminate\Foundation\Auth\User $user */
    $user = Auth::user();
    $success = $user->tokens()->delete();

    return $success;
  }
}
