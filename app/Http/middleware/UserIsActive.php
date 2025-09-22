<?php
// app/Http/Middleware/EnsureUserIsActive.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && ! (Auth::user()->is_active ?? true)) {
            // Cierra la sesión por seguridad
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Respuesta distinta si es API/JSON
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Cuenta desactivada'], 403);
            }

            return redirect()
                ->route('auth-login')
                ->withErrors(['email' => 'Tu cuenta está desactivada.']);
        }

        return $next($request);
    }
}
