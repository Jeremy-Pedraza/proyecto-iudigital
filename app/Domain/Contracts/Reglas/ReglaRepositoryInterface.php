<?php

namespace App\Domain\Contracts\Reglas;

use App\Models\Regla;
use Illuminate\Support\Collection;

interface ReglaRepositoryInterface
{
    /**
     * Obtiene todas las reglas organizadas por categoría
     */
    public function getAllGroupedByCategory(): Collection;

    /**
     * Obtiene todas las reglas
     */
    public function getAll(): Collection;

    /**
     * Obtiene reglas por categoría
     */
    public function getByCategory(string $categoria): Collection;

    /**
     * Obtiene una regla por su código
     */
    public function findByCodigo(string $codigo): ?Regla;

    /**
     * Actualiza una regla
     */
    public function update(Regla $regla, array $data): Regla;

    /**
     * Obtiene solo reglas activas
     */
    public function getActive(): Collection;

    /**
     * Obtiene solo reglas editables
     */
    public function getEditable(): Collection;
}
