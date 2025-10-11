<?php

namespace App\Domain\Contracts\Rutas;

use App\Models\Ruta;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface RutaServiceInterface
{
    /**
     * Listar rutas con filtros y paginación
     */
    public function list(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Obtener datos necesarios para el formulario de planificación
     */
    public function getDataForPlanner(): array;

    /**
     * Crear nueva ruta (borrador)
     */
    public function create(array $data): Ruta;

    /**
     * Generar/calcular paradas de una ruta según algoritmo
     */
    public function generarParadas(Ruta $ruta): Ruta;

    /**
     * Actualizar ruta existente
     */
    public function update(Ruta $ruta, array $data): Ruta;

    /**
     * Eliminar ruta
     */
    public function delete(Ruta $ruta): void;

    /**
     * Buscar ruta por ID
     */
    public function findById(int $id, array $with = [], bool $failIfNotFound = true): ?Ruta;

    /**
     * Publicar ruta (disponible para móvil)
     */
    public function publicar(Ruta $ruta, int $usuarioId): bool;

    /**
     * Validar que no existan solapamientos
     */
    public function validarSolapamiento(array $data, ?int $rutaIdExcluir = null): bool;

    /**
     * Obtener clientes elegibles para planificación
     */
    public function getClientesElegibles(array $filters): Collection;

    /**
     * Calcular métricas de la ruta
     */
    public function calcularMetricas(Ruta $ruta): void;

    /**
     * Reordenar paradas de la ruta (drag & drop)
     */
    public function reordenarParadas(Ruta $ruta, array $nuevoOrden): void;
}
