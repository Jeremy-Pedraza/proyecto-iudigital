<?php

namespace App\Infrastructure\Rutas;

use App\Domain\Contracts\Rutas\RutaRepositoryInterface;
use App\Models\Ruta;
use App\Models\Cliente;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class RutaRepository implements RutaRepositoryInterface
{
    public function paginate(array $f, int $perPage = 15): LengthAwarePaginator
    {
        return Ruta::query()
            ->with(['comercial:id,name', 'paradas'])
            ->when($f['search'] ?? null, function ($q, $s) {
                $q->where('nombre', 'like', "%$s%");
            })
            ->comercial($f['comercial_id'] ?? null)
            ->estado($f['estado'] ?? null)
            ->periodo($f['fecha_inicio'] ?? null, $f['fecha_fin'] ?? null)
            ->recientes()
            ->paginate($perPage);
    }

    public function create(array $data): Ruta
    {
        return Ruta::create($data);
    }

    public function update(Ruta $ruta, array $data): Ruta
    {
        $ruta->update($data);
        return $ruta->fresh();
    }

    public function delete(Ruta $ruta): void
    {
        $ruta->delete();
    }

    public function findById(int $id, array $with = []): ?Ruta
    {
        $q = Ruta::query();
        if (!empty($with)) {
            $q->with($with);
        }
        return $q->find($id);
    }

    public function getRutasByComercialYPeriodo(
        int $comercialId,
        string $fechaInicio,
        string $fechaFin
    ): Collection {
        return Ruta::query()
            ->where('comercial_id', $comercialId)
            ->where(function ($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                    ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                    ->orWhere(function ($qq) use ($fechaInicio, $fechaFin) {
                        $qq->where('fecha_inicio', '<=', $fechaInicio)
                            ->where('fecha_fin', '>=', $fechaFin);
                    });
            })
            ->get();
    }

    public function getClientesDisponibles(array $filters): Collection
    {
        $query = Cliente::query()
            ->with('comercial:id,name')
            ->where('estado', 'activo');

        // Aplicar filtros
        if ($ciudad = $filters['ciudad'] ?? null) {
            $query->ciudad($ciudad);
        }

        if ($frecuencia = $filters['frecuencia'] ?? null) {
            $query->frecuencia($frecuencia);
        }

        if ($prioridadMin = $filters['prioridad_min'] ?? null) {
            $query->where('prioridad', '>=', $prioridadMin);
        }

        if ($comercialId = $filters['comercial_id'] ?? null) {
            $query->comercial($comercialId);
        }

        // Solo clientes con coordenadas válidas
        $query->whereNotNull('lat')
            ->whereNotNull('lng')
            ->where('lat', '!=', 0)
            ->where('lng', '!=', 0);

        return $query->get();
    }

    public function existeSolapamiento(
        int $comercialId,
        string $fechaInicio,
        string $fechaFin,
        ?int $rutaIdExcluir = null
    ): bool {
        $query = Ruta::query()
            ->where('comercial_id', $comercialId)
            ->whereIn('estado', ['publicada', 'en_ejecucion'])
            ->where(function ($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                    ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                    ->orWhere(function ($qq) use ($fechaInicio, $fechaFin) {
                        $qq->where('fecha_inicio', '<=', $fechaInicio)
                            ->where('fecha_fin', '>=', $fechaFin);
                    });
            });

        if ($rutaIdExcluir) {
            $query->where('id', '!=', $rutaIdExcluir);
        }

        return $query->exists();
    }

    public function getEstadisticas(array $filters = []): array
    {
        $query = Ruta::query();

        if ($comercialId = $filters['comercial_id'] ?? null) {
            $query->comercial($comercialId);
        }

        if ($fechaInicio = $filters['fecha_inicio'] ?? null) {
            $query->where('fecha_inicio', '>=', $fechaInicio);
        }

        if ($fechaFin = $filters['fecha_fin'] ?? null) {
            $query->where('fecha_fin', '<=', $fechaFin);
        }

        $rutas = $query->get();

        return [
            'total_rutas' => $rutas->count(),
            'rutas_por_estado' => $rutas->groupBy('estado')->map->count(),
            'total_paradas' => $rutas->sum('total_paradas'),
            'distancia_total_km' => $rutas->sum('distancia_total_km'),
            'tiempo_total_horas' => round($rutas->sum('tiempo_total_minutos') / 60, 2),
            'promedio_paradas_ruta' => $rutas->count() > 0
                ? round($rutas->avg('total_paradas'), 1)
                : 0,
        ];
    }
}
