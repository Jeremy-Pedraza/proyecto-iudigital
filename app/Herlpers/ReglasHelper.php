<?php

namespace App\Helpers;

use App\Domain\Contracts\Reglas\ReglaServiceInterface;
use Illuminate\Support\Facades\Cache;

/**
 * Helper para acceso rápido a reglas de negocio
 * 
 * Uso:
 * - ReglasHelper::get('max_paradas_dia')
 * - ReglasHelper::getInt('max_paradas_dia', 15)
 * - ReglasHelper::getBool('balanceo_rutas', true)
 */
class ReglasHelper
{
    /**
     * Obtiene el valor de una regla por su código
     *
     * @param string $codigo Código de la regla
     * @param mixed $default Valor por defecto si no existe
     * @return mixed
     */
    public static function get(string $codigo, $default = null)
    {
        $service = app(ReglaServiceInterface::class);

        if (config('reglas.cache_enabled', true)) {
            $cacheKey = "regla_{$codigo}";
            $cacheTtl = config('reglas.cache_ttl', 60);

            return Cache::remember($cacheKey, $cacheTtl * 60, function () use ($service, $codigo, $default) {
                $valor = $service->getValorByCodigo($codigo);
                return $valor ?? $default;
            });
        }

        $valor = $service->getValorByCodigo($codigo);
        return $valor ?? $default;
    }

    /**
     * Obtiene el valor como entero
     *
     * @param string $codigo
     * @param int $default
     * @return int
     */
    public static function getInt(string $codigo, int $default = 0): int
    {
        return (int) static::get($codigo, $default);
    }

    /**
     * Obtiene el valor como float
     *
     * @param string $codigo
     * @param float $default
     * @return float
     */
    public static function getFloat(string $codigo, float $default = 0.0): float
    {
        return (float) static::get($codigo, $default);
    }

    /**
     * Obtiene el valor como booleano
     *
     * @param string $codigo
     * @param bool $default
     * @return bool
     */
    public static function getBool(string $codigo, bool $default = false): bool
    {
        $valor = static::get($codigo, $default);
        return filter_var($valor, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Obtiene el valor como string
     *
     * @param string $codigo
     * @param string $default
     * @return string
     */
    public static function getString(string $codigo, string $default = ''): string
    {
        return (string) static::get($codigo, $default);
    }

    /**
     * Obtiene múltiples reglas a la vez
     *
     * @param array $codigos Array de códigos de reglas
     * @return array Asociativo codigo => valor
     */
    public static function getMultiple(array $codigos): array
    {
        $resultado = [];
        foreach ($codigos as $codigo) {
            $resultado[$codigo] = static::get($codigo);
        }
        return $resultado;
    }

    /**
     * Limpia el caché de una regla específica
     *
     * @param string $codigo
     * @return void
     */
    public static function clearCache(string $codigo): void
    {
        Cache::forget("regla_{$codigo}");
    }

    /**
     * Limpia el caché de todas las reglas
     *
     * @return void
     */
    public static function clearAllCache(): void
    {
        // Esto es una aproximación básica
        // En producción, considera usar tags de caché o un prefijo específico
        Cache::flush();
    }

    /**
     * Verifica si una regla está activa
     *
     * @param string $codigo
     * @return bool
     */
    public static function isActive(string $codigo): bool
    {
        $service = app(ReglaServiceInterface::class);
        $reglaService = app(\App\Domain\Contracts\Reglas\ReglaRepositoryInterface::class);

        $regla = $reglaService->findByCodigo($codigo);
        return $regla ? $regla->activa : false;
    }

    /**
     * Obtiene todas las reglas de una categoría
     *
     * @param string $categoria
     * @return \Illuminate\Support\Collection
     */
    public static function getByCategory(string $categoria)
    {
        $service = app(ReglaServiceInterface::class);
        return $service->getByCategory($categoria);
    }

    /**
     * Obtiene la configuración completa de capacidad
     *
     * @return array
     */
    public static function getCapacidadConfig(): array
    {
        return [
            'max_paradas' => static::getInt('max_paradas_dia', 15),
            'min_paradas' => static::getInt('min_paradas_dia', 5),
        ];
    }

    /**
     * Obtiene la configuración completa de tiempo
     *
     * @return array
     */
    public static function getTiempoConfig(): array
    {
        return [
            'duracion_max_jornada' => static::getInt('duracion_max_jornada', 480),
            'tiempo_pausa' => static::getInt('tiempo_pausa', 60),
            'tiempo_promedio_parada' => static::getInt('tiempo_promedio_parada', 20),
            'hora_inicio' => static::getString('hora_inicio_jornada', '08:00'),
            'hora_fin' => static::getString('hora_fin_jornada', '18:00'),
        ];
    }

    /**
     * Obtiene la configuración completa de distancias
     *
     * @return array
     */
    public static function getDistanciaConfig(): array
    {
        return [
            'distancia_max_ruta' => static::getFloat('distancia_max_ruta', 150),
            'radio_geocerca' => static::getFloat('radio_geocerca_checkin', 100),
        ];
    }

    /**
     * Obtiene la configuración de optimización
     *
     * @return array
     */
    public static function getOptimizacionConfig(): array
    {
        return [
            'algoritmo' => static::getString('algoritmo_optimizacion', 'nearest_neighbor'),
            'priorizar_por' => static::getString('priorizar_por', 'prioridad'),
            'balanceo' => static::getBool('balanceo_rutas', true),
        ];
    }

    /**
     * Obtiene la configuración de restricciones
     *
     * @return array
     */
    public static function getRestriccionesConfig(): array
    {
        return [
            'respetar_ventanas' => static::getBool('respetar_ventanas_horarias', true),
            'respetar_frecuencias' => static::getBool('respetar_frecuencias', true),
            'permitir_fuera_frecuencia' => static::getBool('permitir_visitas_fuera_frecuencia', false),
        ];
    }
}
