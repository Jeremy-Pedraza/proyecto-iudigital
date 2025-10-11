<?php

namespace App\Domain\Contracts\Rutas;

use App\Models\Ruta;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface RutaRepositoryInterface
{
    /**
     * Obtener listado paginado de rutas con filtros
     */
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Crear nueva ruta
     */
    public function create(array $data): Ruta;

    /**
     * Actualizar ruta existente
     */
    public function update(Ruta $ruta, array $data): Ruta;

    /**
     * Eliminar ruta (soft delete)
     */
    public function delete(Ruta $ruta): void;

    /**
     * Buscar ruta por ID con relaciones opcionales
     */
    public function findById(int $id, array $with = []): ?Ruta;

    /**
     * Obtener rutas de un comercial en un período
     */
    public function getRutasByComercialYPeriodo(
        int $comercialId,
        string $fechaInicio,
        string $fechaFin
    ): Collection;

    /**
     * Obtener clientes disponibles para planificación
     * (filtrados según criterios: ciudad, frecuencia, estado, etc.)
     */
    public function getClientesDisponibles(array $filters): Collection;

    /**
     * Verificar si existe solapamiento de rutas para un comercial
     */
    public function existeSolapamiento(
        int $comercialId,
        string $fechaInicio,
        string $fechaFin,
        ?int $rutaIdExcluir = null
    ): bool;

    /**
     * Obtener estadísticas de rutas
     */
    public function getEstadisticas(array $filters = []): array;

    /**
     * Actualizar el orden secuencial de las paradas de una ruta
     */
    public function actualizarOrdenParadas(Ruta $ruta, array $orden): void;
}
