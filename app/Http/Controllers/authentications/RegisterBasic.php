<?php

namespace App\Http\Controllers\authentications;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Controllers\Controller;
use App\Infrastructure\Users\RegisterRepository;
use Illuminate\Validation\ValidationException;


class RegisterBasic extends Controller
{
  protected $users;

  public function __construct(RegisterRepository $users)
  {
    $this->users = $users;
  }
  public function index()
  {
    return view('content.authentications.auth-register-basic');
  }
  public function register(RegisterRequest $request)
  {
    try {
      // Validar los datos del formulario
        $credentials = $request->validated();

        // Crear el usuario
        $this->users->create($credentials);

        // Redirigir al usuario a la página de inicio de sesión con un mensaje de éxito
        return redirect()->route('auth-login')->with('success', 'Registro exitoso. Por favor, inicia sesión.');
      } catch (ValidationException $e) {
        return back()->withErrors(['error' => 'The provided credentials do not match our records.']);
    }
  }
}
