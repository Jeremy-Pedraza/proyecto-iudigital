<?php

namespace App\Http\Controllers\authentications;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class LoginBasic extends Controller
{
  public function index()
  {
    return view('content.authentications.auth-login-basic');
  }

  public function login(LoginRequest $request)
  {
    // Solo toma los campos necesarios del request validado
    $credentials = $request->safe()->only(['email', 'password']);

    // Fuerza que solo se consulte usuarios activos
    $credentials['is_active'] = true;

    // Castea correctamente el checkbox
    $remember = $request->boolean('remember');

    if (Auth::attempt($credentials, $remember)) {
      $request->session()->regenerate();

      // Marca último login
      /** @var User $user */
      $user = Auth::user();
      $user->markLastLogin();

      return redirect()->route('dashboard-analytics');
    }

    // Si quieres diferenciar mensaje para inactivos:
    // - Busca al usuario por email, y si existe pero está inactivo, responde distinto
    if ($u = User::where('email', $credentials['email'])->first()) {
      if (!$u->isActive()) {
        return back()
          ->withInput($request->only('email', 'remember'))
          ->withErrors(['email' => 'Tu cuenta está inactiva. Contacta al administrador.']);
      }
    }

    return back()
      ->withInput($request->only('email', 'remember'))
      ->withErrors(['email' => 'Credenciales inválidas']);
  }

  public function logout(Request $request)
  {
    // Cierra la sesión (incluye limpiar cookie "remember me" y token en BD)
    Auth::logout();

    // Invalida la sesión actual
    $request->session()->invalidate();

    // Regenera el token CSRF
    $request->session()->regenerateToken();

    // Redirige al login con un mensaje
    return redirect()->route('auth-login')->with('success', 'Sesión cerrada correctamente.');
  }
}
