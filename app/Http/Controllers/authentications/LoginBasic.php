<?php

namespace App\Http\Controllers\authentications;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginBasic extends Controller
{
  public function index()
  {
    return view('content.authentications.auth-login-basic');
  }

  public function login(LoginRequest $request)
  {

    // Validate the request...
    $credentials = $request->validated();
    $remember = $credentials['remember'] ?? false;
    unset($credentials['remember']);
    // Attempt login...
    if (Auth::attempt($credentials, $remember)) {
      $request->session()->regenerate();
      return redirect()->route('dashboard-analytics');
    }

    return redirect()->back()->withErrors(['email' => 'Invalid credentials']);
  }
}
