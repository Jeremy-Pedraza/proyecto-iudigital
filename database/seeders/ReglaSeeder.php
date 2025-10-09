<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReglaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reglas = [
            // CAPACIDAD
            [
                'codigo' => 'max_paradas_dia',
                'nombre' => 'Máximo de paradas por día',
                'descripcion' => 'Número máximo de paradas que un comercial puede realizar en un día de trabajo',
                'tipo' => 'numero',
                'valor' => '15',
                'valor_minimo' => 1,
                'valor_maximo' => 50,
                'unidad' => 'paradas',
                'categoria' => 'capacidad',
                'activa' => true,
                'editable' => true,
                'orden' => 1
            ],
            [
                'codigo' => 'min_paradas_dia',
                'nombre' => 'Mínimo de paradas por día',
                'descripcion' => 'Número mínimo de paradas que debe tener una ruta para ser considerada válida',
                'tipo' => 'numero',
                'valor' => '5',
                'valor_minimo' => 1,
                'valor_maximo' => 20,
                'unidad' => 'paradas',
                'categoria' => 'capacidad',
                'activa' => true,
                'editable' => true,
                'orden' => 2
            ],

            // TIEMPO
            [
                'codigo' => 'duracion_max_jornada',
                'nombre' => 'Duración máxima de jornada',
                'descripcion' => 'Tiempo máximo que puede durar una jornada de trabajo en minutos',
                'tipo' => 'tiempo',
                'valor' => '480',
                'valor_minimo' => 180,
                'valor_maximo' => 720,
                'unidad' => 'minutos',
                'categoria' => 'tiempo',
                'activa' => true,
                'editable' => true,
                'orden' => 10
            ],
            [
                'codigo' => 'tiempo_pausa',
                'nombre' => 'Tiempo de pausa/almuerzo',
                'descripcion' => 'Tiempo destinado para pausa o almuerzo durante la jornada',
                'tipo' => 'tiempo',
                'valor' => '60',
                'valor_minimo' => 0,
                'valor_maximo' => 120,
                'unidad' => 'minutos',
                'categoria' => 'tiempo',
                'activa' => true,
                'editable' => true,
                'orden' => 11
            ],
            [
                'codigo' => 'tiempo_promedio_parada',
                'nombre' => 'Tiempo promedio por parada',
                'descripcion' => 'Tiempo estimado que toma atender cada cliente',
                'tipo' => 'tiempo',
                'valor' => '20',
                'valor_minimo' => 5,
                'valor_maximo' => 120,
                'unidad' => 'minutos',
                'categoria' => 'tiempo',
                'activa' => true,
                'editable' => true,
                'orden' => 12
            ],
            [
                'codigo' => 'hora_inicio_jornada',
                'nombre' => 'Hora de inicio de jornada',
                'descripcion' => 'Hora en que inicia la jornada laboral (formato HH:MM)',
                'tipo' => 'texto',
                'valor' => '08:00',
                'unidad' => 'hora',
                'categoria' => 'tiempo',
                'activa' => true,
                'editable' => true,
                'orden' => 13
            ],
            [
                'codigo' => 'hora_fin_jornada',
                'nombre' => 'Hora de fin de jornada',
                'descripcion' => 'Hora en que finaliza la jornada laboral (formato HH:MM)',
                'tipo' => 'texto',
                'valor' => '18:00',
                'unidad' => 'hora',
                'categoria' => 'tiempo',
                'activa' => true,
                'editable' => true,
                'orden' => 14
            ],

            // DISTANCIA
            [
                'codigo' => 'distancia_max_ruta',
                'nombre' => 'Distancia máxima por ruta',
                'descripcion' => 'Distancia máxima total que puede recorrer una ruta en kilómetros',
                'tipo' => 'numero',
                'valor' => '150',
                'valor_minimo' => 10,
                'valor_maximo' => 500,
                'unidad' => 'km',
                'categoria' => 'distancia',
                'activa' => true,
                'editable' => true,
                'orden' => 20
            ],
            [
                'codigo' => 'radio_geocerca_checkin',
                'nombre' => 'Radio de geocerca para check-in',
                'descripcion' => 'Distancia máxima desde el cliente para permitir check-in automático',
                'tipo' => 'numero',
                'valor' => '100',
                'valor_minimo' => 10,
                'valor_maximo' => 500,
                'unidad' => 'metros',
                'categoria' => 'distancia',
                'activa' => true,
                'editable' => true,
                'orden' => 21
            ],

            // OPTIMIZACIÓN
            [
                'codigo' => 'algoritmo_optimizacion',
                'nombre' => 'Algoritmo de optimización',
                'descripcion' => 'Algoritmo utilizado para calcular rutas óptimas',
                'tipo' => 'texto',
                'valor' => 'nearest_neighbor',
                'unidad' => null,
                'categoria' => 'optimizacion',
                'activa' => true,
                'editable' => true,
                'orden' => 30
            ],
            [
                'codigo' => 'priorizar_por',
                'nombre' => 'Criterio de priorización',
                'descripcion' => 'Criterio principal para priorizar clientes en las rutas',
                'tipo' => 'texto',
                'valor' => 'prioridad',
                'unidad' => null,
                'categoria' => 'optimizacion',
                'activa' => true,
                'editable' => true,
                'orden' => 31
            ],
            [
                'codigo' => 'balanceo_rutas',
                'nombre' => 'Balanceo automático de rutas',
                'descripcion' => 'Distribuir equitativamente las paradas entre comerciales',
                'tipo' => 'booleano',
                'valor' => 'true',
                'unidad' => null,
                'categoria' => 'optimizacion',
                'activa' => true,
                'editable' => true,
                'orden' => 32
            ],

            // RESTRICCIONES
            [
                'codigo' => 'respetar_ventanas_horarias',
                'nombre' => 'Respetar ventanas horarias',
                'descripcion' => 'Las rutas deben respetar las ventanas horarias definidas por cliente',
                'tipo' => 'booleano',
                'valor' => 'true',
                'unidad' => null,
                'categoria' => 'restricciones',
                'activa' => true,
                'editable' => true,
                'orden' => 40
            ],
            [
                'codigo' => 'respetar_frecuencias',
                'nombre' => 'Respetar frecuencias de visita',
                'descripcion' => 'Las rutas deben respetar la frecuencia de visita definida por cliente',
                'tipo' => 'booleano',
                'valor' => 'true',
                'unidad' => null,
                'categoria' => 'restricciones',
                'activa' => true,
                'editable' => true,
                'orden' => 41
            ],
            [
                'codigo' => 'permitir_visitas_fuera_frecuencia',
                'nombre' => 'Permitir visitas fuera de frecuencia',
                'descripcion' => 'Permite realizar visitas adicionales a clientes fuera de su frecuencia establecida',
                'tipo' => 'booleano',
                'valor' => 'false',
                'unidad' => null,
                'categoria' => 'restricciones',
                'activa' => true,
                'editable' => true,
                'orden' => 42
            ],

            // PENALIZACIONES
            [
                'codigo' => 'penalizacion_ventana_horaria',
                'nombre' => 'Penalización por incumplir ventana horaria',
                'descripcion' => 'Factor de penalización al calcular rutas que no respetan ventanas horarias',
                'tipo' => 'numero',
                'valor' => '10',
                'valor_minimo' => 0,
                'valor_maximo' => 100,
                'unidad' => 'factor',
                'categoria' => 'penalizaciones',
                'activa' => true,
                'editable' => true,
                'orden' => 50
            ],
            [
                'codigo' => 'penalizacion_prioridad_baja',
                'nombre' => 'Penalización por baja prioridad',
                'descripcion' => 'Factor de penalización para clientes con prioridad baja',
                'tipo' => 'numero',
                'valor' => '5',
                'valor_minimo' => 0,
                'valor_maximo' => 50,
                'unidad' => 'factor',
                'categoria' => 'penalizaciones',
                'activa' => true,
                'editable' => true,
                'orden' => 51
            ],
            [
                'codigo' => 'penalizacion_distancia_excesiva',
                'nombre' => 'Penalización por distancia excesiva',
                'descripcion' => 'Factor de penalización cuando una parada está muy alejada de la ruta',
                'tipo' => 'numero',
                'valor' => '15',
                'valor_minimo' => 0,
                'valor_maximo' => 100,
                'unidad' => 'factor',
                'categoria' => 'penalizaciones',
                'activa' => true,
                'editable' => true,
                'orden' => 52
            ],

            // GENERAL
            [
                'codigo' => 'modo_offline_habilitado',
                'nombre' => 'Modo offline habilitado',
                'descripcion' => 'Permite que la aplicación móvil funcione sin conexión a internet',
                'tipo' => 'booleano',
                'valor' => 'true',
                'unidad' => null,
                'categoria' => 'general',
                'activa' => true,
                'editable' => true,
                'orden' => 60
            ],
            [
                'codigo' => 'sincronizacion_automatica',
                'nombre' => 'Sincronización automática',
                'descripcion' => 'Sincronizar automáticamente datos cuando hay conexión disponible',
                'tipo' => 'booleano',
                'valor' => 'true',
                'unidad' => null,
                'categoria' => 'general',
                'activa' => true,
                'editable' => true,
                'orden' => 61
            ]
        ];

        foreach ($reglas as $regla) {
            DB::table('reglas')->insert(array_merge($regla, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}
