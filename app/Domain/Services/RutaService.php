<?php

namespace App\Domain\Services;

use App\Domain\Contracts\Rutas\{RutaRepositoryInterface, RutaServiceInterface};
use App\Models\{Ruta, User, Cliente};
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use RuntimeException;
use Carbon\Carbon;

class RutaService implements RutaServiceInterface
{
    public function __construct(private RutaRepositoryInterface $repo) {}

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->paginate($filters, $perPage);
    }

    public function getDataForPlanner(): array
    {
        // Obtener comerciales activos
        $comerciales = User::query()
            ->whereHas('roles', function ($q) {
                $q->whereIn('slug', ['supervisor', 'cobranzas']);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Obtener ciudades únicas de clientes activos
        $ciudades = Cliente::query()
            ->where('estado', 'activo')
            ->distinct()
            ->pluck('ciudad')
            ->filter()
            ->sort()
            ->values();

        // Frecuencias disponibles
        $frecuencias = [
            'diaria' => 'Diaria',
            'semanal' => 'Semanal',
            'quincenal' => 'Quincenal',
            'mensual' => 'Mensual',
        ];

        // Criterios de priorización
        $criterios = [
            'distancia' => 'Minimizar distancia',
            'tiempo' => 'Minimizar tiempo',
            'prioridad_cliente' => 'Prioridad de clientes',
            'balanceado' => 'Balanceado',
        ];

        return [
            'comerciales' => $comerciales,
            'ciudades' => $ciudades,
            'frecuencias' => $frecuencias,
            'criterios_optimizacion' => $criterios,
            'max_paradas_default' => 20,
            'duracion_pausa_default' => 60,
            'hora_inicio_default' => '08:00',
            'hora_fin_default' => '18:00',
        ];
    }

    public function create(array $data): Ruta
    {
        // Normalizar datos
        $data['estado'] = 'borrador';
        $data['total_paradas'] = 0;
        $data['dias_planificados'] = 0;

        // Calcular días del período
        $fechaInicio = Carbon::parse($data['fecha_inicio']);
        $fechaFin = Carbon::parse($data['fecha_fin']);
        $dias = $fechaInicio->diffInDays($fechaFin) + 1;

        // Generar nombre automático si no se proporciona
        if (empty($data['nombre'])) {
            $comercial = User::find($data['comercial_id']);
            $data['nombre'] = sprintf(
                'Ruta %s - %s',
                $comercial?->name ?? 'Sin asignar',
                $fechaInicio->format('d/m/Y')
            );
        }

        return $this->repo->create($data);
    }

    public function generarParadas(Ruta $ruta): Ruta
    {
        // Validar que esté en estado borrador
        if ($ruta->estado !== 'borrador') {
            throw new RuntimeException('Solo se pueden generar paradas para rutas en borrador');
        }

        // Obtener clientes elegibles
        $filtros = array_merge(
            $ruta->filtros_clientes ?? [],
            ['comercial_id' => $ruta->comercial_id]
        );

        $clientes = $this->getClientesElegibles($filtros);

        if ($clientes->isEmpty()) {
            throw new RuntimeException('No hay clientes disponibles con los filtros aplicados');
        }

        // Generar paradas según el algoritmo
        $paradas = $this->calcularRutaOptima($ruta, $clientes);

        // Guardar paradas
        $ruta->paradas()->delete(); // Limpiar paradas anteriores
        $ruta->paradas()->createMany($paradas);

        // Actualizar estado y métricas
        $ruta->estado = 'calculada';
        $ruta->save();
        $this->calcularMetricas($ruta);

        return $ruta->fresh(['paradas', 'comercial']);
    }

    public function update(Ruta $ruta, array $data): Ruta
    {
        // Si está publicada, solo permitir actualizar ciertos campos
        if ($ruta->es_publicada) {
            $data = array_intersect_key($data, array_flip(['notas', 'estado']));
        }

        return $this->repo->update($ruta, $data);
    }

    public function delete(Ruta $ruta): void
    {
        // No permitir eliminar rutas en ejecución
        if ($ruta->estado === 'en_ejecucion') {
            throw new RuntimeException('No se puede eliminar una ruta en ejecución');
        }

        $this->repo->delete($ruta);
    }

    public function findById(int $id, array $with = [], bool $failIfNotFound = true): ?Ruta
    {
        $ruta = $this->repo->findById($id, $with);

        if (!$ruta && $failIfNotFound) {
            throw (new ModelNotFoundException())->setModel(Ruta::class, [$id]);
        }

        return $ruta;
    }

    public function publicar(Ruta $ruta, int $usuarioId): bool
    {
        if ($ruta->estado !== 'calculada') {
            throw new RuntimeException('Solo se pueden publicar rutas calculadas');
        }

        if ($ruta->total_paradas === 0) {
            throw new RuntimeException('No se puede publicar una ruta sin paradas');
        }

        return $ruta->publicar($usuarioId);
    }

    public function validarSolapamiento(array $data, ?int $rutaIdExcluir = null): bool
    {
        return $this->repo->existeSolapamiento(
            $data['comercial_id'],
            $data['fecha_inicio'],
            $data['fecha_fin'],
            $rutaIdExcluir
        );
    }

    public function getClientesElegibles(array $filters): Collection
    {
        return $this->repo->getClientesDisponibles($filters);
    }

    public function calcularMetricas(Ruta $ruta): void
    {
        $ruta->calcularMetricas();
    }

    /**
     * Algoritmo simplificado de generación de rutas
     * En producción, aquí iría un algoritmo más sofisticado (TSP, VRP, etc.)
     */
    private function calcularRutaOptima(Ruta $ruta, Collection $clientes): array
    {
        $paradas = [];
        $fechaInicio = Carbon::parse($ruta->fecha_inicio);
        $fechaFin = Carbon::parse($ruta->fecha_fin);

        // Dividir clientes por días según frecuencia
        $clientesPorDia = $this->distribuirClientesPorDias(
            $clientes,
            $fechaInicio,
            $fechaFin,
            $ruta->max_paradas_dia
        );

        foreach ($clientesPorDia as $fecha => $clientesDia) {
            // Ordenar clientes por proximidad (simplificado - en producción usar algoritmo TSP)
            $clientesOrdenados = $this->ordenarPorProximidad($clientesDia);

            $horaActual = Carbon::parse($ruta->hora_inicio_jornada);
            $orden = 1;

            foreach ($clientesOrdenados as $index => $cliente) {
                $paradaData = [
                    'cliente_id' => $cliente->id,
                    'fecha_planificada' => $fecha,
                    'orden_secuencia' => $orden,
                    'hora_estimada_llegada' => $horaActual->format('H:i:s'),
                    'duracion_estimada_minutos' => 30,
                    'estado' => 'pendiente',
                ];

                // Calcular distancia y tiempo desde parada anterior
                if ($index > 0) {
                    $clienteAnterior = $clientesOrdenados[$index - 1];
                    $distancia = $this->calcularDistancia(
                        $clienteAnterior->lat,
                        $clienteAnterior->lng,
                        $cliente->lat,
                        $cliente->lng
                    );
                    $tiempoViaje = $this->calcularTiempoViaje($distancia);

                    $paradaData['distancia_desde_anterior_km'] = $distancia;
                    $paradaData['tiempo_desde_anterior_minutos'] = $tiempoViaje;

                    $horaActual->addMinutes($tiempoViaje);
                }

                $paradaData['hora_estimada_salida'] = $horaActual->addMinutes(30)->format('H:i:s');

                $paradas[] = $paradaData;
                $orden++;
            }
        }

        return $paradas;
    }

    private function distribuirClientesPorDias(
        Collection $clientes,
        Carbon $fechaInicio,
        Carbon $fechaFin,
        int $maxParadasDia
    ): array {
        $resultado = [];
        $diasDisponibles = [];

        // Generar lista de días laborables
        $fecha = $fechaInicio->copy();
        while ($fecha->lte($fechaFin)) {
            if ($fecha->isWeekday()) { // Solo días laborables
                $diasDisponibles[] = $fecha->format('Y-m-d');
            }
            $fecha->addDay();
        }

        // Distribución simple: round-robin
        $diaIndex = 0;
        $contadorPorDia = array_fill_keys($diasDisponibles, 0);

        foreach ($clientes as $cliente) {
            // Buscar día con menos paradas
            $diaSeleccionado = $diasDisponibles[$diaIndex];

            if (!isset($resultado[$diaSeleccionado])) {
                $resultado[$diaSeleccionado] = collect();
            }

            if ($contadorPorDia[$diaSeleccionado] < $maxParadasDia) {
                $resultado[$diaSeleccionado]->push($cliente);
                $contadorPorDia[$diaSeleccionado]++;
            }

            $diaIndex = ($diaIndex + 1) % count($diasDisponibles);
        }

        return $resultado;
    }

    private function ordenarPorProximidad(Collection $clientes): Collection
    {
        // Algoritmo simple: nearest neighbor desde el primer cliente
        // En producción: implementar TSP o usar librería especializada
        return $clientes->sortBy('prioridad', SORT_REGULAR, true);
    }

    private function calcularDistancia(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $radioTierra = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($radioTierra * $c, 2);
    }

    private function calcularTiempoViaje(float $distanciaKm): int
    {
        // Estimación simple: 40 km/h promedio en ciudad
        $velocidadPromedio = 40;
        return (int) round(($distanciaKm / $velocidadPromedio) * 60);
    }
}
