<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // 👈 importa DB para expresiones RAW
use App\Models\User;

class RoleMiddleware
{
    /**
     * Uso en rutas: ->middleware('role:Admin|Supervisor')
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        if (!Auth::check()) {
            return $this->deny($request, 'No autenticado.', 401);
        }

        /** @var User $user */
        $user = Auth::user();

        // Normaliza y soporta múltiples roles separados por '|'
        $required = collect(explode('|', (string) $roles))
            ->map(fn($r) => trim(mb_strtolower($r)))
            ->filter()
            ->values()
            ->all();

        // Valida contra la relación many-to-many SIN cargarla completa
        $hasRole = $user->roles()
            ->where(function ($q) use ($required) {
                $q->whereIn(DB::raw('LOWER(name)'), $required)
                    ->orWhereIn(DB::raw('LOWER(slug)'), $required);
            })
            ->exists();

        // Respeta flag de actividad si lo usas
        if (!$user->isActive()) {
            return $this->deny($request, 'Usuario inactivo.', 403);
        }

        if (!$hasRole) {
            return $this->deny($request, 'No tienes permisos para acceder aquí.', 403);
        }

        return $next($request);
    }

    private function deny(Request $request, string $message, int $status)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }
        if ($status === 401) {
            return redirect()->route('login')->withErrors(['auth' => $message]);
        }
        abort($status, $message);
    }
}
