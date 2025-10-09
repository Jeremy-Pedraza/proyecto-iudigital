<?php

namespace App\Observers;

use App\Models\Regla;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ReglaObserver
{
    /**
     * Handle the Regla "updating" event.
     * 
     * Se ejecuta ANTES de actualizar la regla
     */
    public function updating(Regla $regla): void
    {
        // Guardar el valor anterior para auditoría
        $regla->valor_anterior = $regla->getOriginal('valor');
        $regla->activa_anterior = $regla->getOriginal('activa');
    }

    /**
     * Handle the Regla "updated" event.
     * 
     * Se ejecuta DESPUÉS de actualizar la regla
     */
    public function updated(Regla $regla): void
    {
        if (!config('reglas.auditoria.enabled', true)) {
            return;
        }

        $cambios = $regla->getChanges();

        // Si hubo cambios significativos, registrarlos
        if (!empty($cambios)) {
            $this->logCambio($regla, $cambios);
        }

        // Limpiar caché si está habilitado
        if (config('reglas.cache_enabled', true)) {
            \Illuminate\Support\Facades\Cache::forget("regla_{$regla->codigo}");
        }
    }

    /**
     * Registra el cambio en el log
     */
    private function logCambio(Regla $regla, array $cambios): void
    {
        $user = Auth::user();
        $userName = $user ? $user->name : 'Sistema';
        $userId = $user ? $user->id : null;

        $mensaje = sprintf(
            "Regla '%s' (%s) actualizada por %s (ID: %s)",
            $regla->nombre,
            $regla->codigo,
            $userName,
            $userId ?? 'N/A'
        );

        $contexto = [
            'regla_id' => $regla->id,
            'regla_codigo' => $regla->codigo,
            'regla_nombre' => $regla->nombre,
            'usuario_id' => $userId,
            'usuario_nombre' => $userName,
            'cambios' => $cambios,
            'valor_anterior' => $regla->valor_anterior ?? null,
            'valor_nuevo' => $regla->valor,
            'timestamp' => now()->toIso8601String(),
        ];

        // Si existe el modelo Auditoria, crear registro
        if (class_exists(\App\Models\Auditoria::class)) {
            try {
                \App\Models\Auditoria::create([
                    'usuario_id' => $userId,
                    'accion' => 'update',
                    'modelo' => Regla::class,
                    'modelo_id' => $regla->id,
                    'descripcion' => $mensaje,
                    'datos_anteriores' => json_encode([
                        'valor' => $regla->valor_anterior,
                        'activa' => $regla->activa_anterior ?? $regla->getOriginal('activa'),
                    ]),
                    'datos_nuevos' => json_encode($cambios),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Exception $e) {
                Log::channel(config('reglas.auditoria.log_channel', 'daily'))
                    ->error('Error al crear registro de auditoría: ' . $e->getMessage());
            }
        }

        // Log en archivo
        Log::channel(config('reglas.auditoria.log_channel', 'daily'))
            ->info($mensaje, $contexto);
    }

    /**
     * Handle the Regla "deleting" event.
     * 
     * Prevenir eliminación accidental de reglas críticas
     */
    public function deleting(Regla $regla): bool
    {
        $reglasCriticas = config('reglas.reglas_criticas', []);

        if (in_array($regla->codigo, $reglasCriticas)) {
            Log::channel(config('reglas.auditoria.log_channel', 'daily'))
                ->warning("Intento de eliminar regla crítica bloqueado: {$regla->codigo}");

            // Prevenir eliminación
            return false;
        }

        return true;
    }

    /**
     * Handle the Regla "deleted" event.
     */
    public function deleted(Regla $regla): void
    {
        if (!config('reglas.auditoria.enabled', true)) {
            return;
        }

        $user = Auth::user();
        $userName = $user ? $user->name : 'Sistema';
        $userId = $user ? $user->id : null;

        $mensaje = sprintf(
            "Regla '%s' (%s) eliminada por %s (ID: %s)",
            $regla->nombre,
            $regla->codigo,
            $userName,
            $userId ?? 'N/A'
        );

        Log::channel(config('reglas.auditoria.log_channel', 'daily'))
            ->warning($mensaje, [
                'regla_id' => $regla->id,
                'regla_codigo' => $regla->codigo,
                'regla_datos' => $regla->toArray(),
                'usuario_id' => $userId,
                'usuario_nombre' => $userName,
                'timestamp' => now()->toIso8601String(),
            ]);

        // Limpiar caché
        if (config('reglas.cache_enabled', true)) {
            \Illuminate\Support\Facades\Cache::forget("regla_{$regla->codigo}");
        }
    }
}
