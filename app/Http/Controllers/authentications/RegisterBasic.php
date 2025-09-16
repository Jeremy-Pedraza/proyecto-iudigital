<?php

namespace App\Http\Controllers\authentications;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;


class RegisterBasic extends Controller
{
  protected $users;

  public function __construct(UserRepository $users)
  {
    $this->users = $users;
  }
  public function index()
  {
    return view('content.authentications.auth-register-basic');
  }
  public function register(RegisterRequest $request)
  {
    // Validar los datos del formulario
    $credentials = $request->validated();

    // Crear el usuario
    $this->users->create($credentials);

    // Redirigir al usuario a la página de inicio de sesión con un mensaje de éxito
    return redirect()->route('auth-login')
      ->with('success', 'Registro exitoso. Por favor, inicia sesión.');
  }
}
