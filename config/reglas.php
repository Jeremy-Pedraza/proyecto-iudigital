<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Caché de Reglas
    |--------------------------------------------------------------------------
    |
    | Define si las reglas deben ser cacheadas para mejorar el rendimiento.
    | Se recomienda activar en producción.
    |
    */
    'cache_enabled' => env('REGLAS_CACHE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Tiempo de Caché (minutos)
    |--------------------------------------------------------------------------
    |
    | Tiempo que las reglas permanecen en caché antes de refrescarse.
    |
    */
    'cache_ttl' => env('REGLAS_CACHE_TTL', 60),

    /*
    |--------------------------------------------------------------------------
    | Categorías de Reglas
    |--------------------------------------------------------------------------
    |
    | Definición de categorías disponibles y sus propiedades.
    |
    */
    'categorias' => [
        'capacidad' => [
            'nombre' => 'Capacidad',
            'icono' => 'boxes-stacked',
            'descripcion' => 'Límites de paradas y capacidad de carga',
        ],
        'tiempo' => [
            'nombre' => 'Tiempo y Jornada',
            'icono' => 'clock',
            'descripcion' => 'Configuración de horarios y tiempos',
        ],
        'distancia' => [
            'nombre' => 'Distancias',
            'icono' => 'road',
            'descripcion' => 'Límites de distancias y geocercas',
        ],
        'optimizacion' => [
            'nombre' => 'Optimización',
            'icono' => 'diagram-project',
            'descripcion' => 'Parámetros del algoritmo de optimización',
        ],
        'restricciones' => [
            'nombre' => 'Restricciones',
            'icono' => 'shield-halved',
            'descripcion' => 'Restricciones de negocio y validaciones',
        ],
        'penalizaciones' => [
            'nombre' => 'Penalizaciones',
            'icono' => 'exclamation-triangle',
            'descripcion' => 'Factores de penalización',
        ],
        'general' => [
            'nombre' => 'General',
            'icono' => 'gear',
            'descripcion' => 'Configuraciones generales del sistema',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipos de Reglas
    |--------------------------------------------------------------------------
    |
    | Tipos de datos soportados para las reglas.
    |
    */
    'tipos' => [
        'numero' => [
            'nombre' => 'Número',
            'validacion' => 'numeric',
        ],
        'booleano' => [
            'nombre' => 'Booleano (Sí/No)',
            'validacion' => 'boolean',
        ],
        'texto' => [
            'nombre' => 'Texto',
            'validacion' => 'string',
        ],
        'tiempo' => [
            'nombre' => 'Tiempo (minutos)',
            'validacion' => 'integer',
        ],
        'json' => [
            'nombre' => 'JSON',
            'validacion' => 'json',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reglas Críticas
    |--------------------------------------------------------------------------
    |
    | Códigos de reglas que requieren confirmación antes de modificarse.
    |
    */
    'reglas_criticas' => [
        'max_paradas_dia',
        'duracion_max_jornada',
        'algoritmo_optimizacion',
    ],

    /*
    |--------------------------------------------------------------------------
    | Valores por Defecto
    |--------------------------------------------------------------------------
    |
    | Valores que se usan cuando una regla no está definida.
    |
    */
    'defaults' => [
        'max_paradas_dia' => 15,
        'duracion_max_jornada' => 480,
        'tiempo_pausa' => 60,
        'tiempo_promedio_parada' => 20,
        'distancia_max_ruta' => 150,
        'radio_geocerca_checkin' => 100,
        'balanceo_rutas' => true,
        'respetar_ventanas_horarias' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Auditoría
    |--------------------------------------------------------------------------
    |
    | Configuración de auditoría de cambios en reglas.
    |
    */
    'auditoria' => [
        'enabled' => env('REGLAS_AUDITORIA_ENABLED', true),
        'log_channel' => env('REGLAS_LOG_CHANNEL', 'daily'),
    ],

];
