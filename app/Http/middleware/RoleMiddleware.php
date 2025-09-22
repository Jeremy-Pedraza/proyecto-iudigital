<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Closure;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $roles)
    {


        if (!Auth::check()) {
            // Si quieres redirigir al login en lugar de 403:
            // return redirect()->route('auth-login');
            abort(403, 'No autenticado.');
        }

        $user = Auth::user();

        // Soporta varios roles separados por '|', ej: Admin|Supervisor
        $allowed = collect(explode('|', $roles))->contains(function ($role) use ($user) {
            $role = trim($role);

            // Ajusta una de estas dos líneas según tu modelo:
            // 1) Si tienes una columna simple 'role' en users:
            // return strcasecmp($user->role, $role) === 0;

            // 2) Si tienes relación roles() many-to-many:
            return method_exists($user, 'roles')
                ? $user->roles->pluck('name')->contains($role)
                : false;
        });

        if (!$allowed) {
            abort(403, 'No tienes permisos para acceder aquí.');
        }

        return $next($request);
    }
}
