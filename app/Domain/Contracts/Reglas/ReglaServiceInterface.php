<?php

namespace App\Domain\Contracts\Reglas;

use App\Models\Regla;
use Illuminate\Support\Collection;

interface ReglaServiceInterface
{
    /**
     * Lista todas las reglas agrupadas por categoría
     */
    public function listGroupedByCategory(): Collection;

    /**
     * Lista todas las reglas
     */
    public function list(): Collection;

    /**
     * Obtiene reglas por categoría
     */
    public function getByCategory(string $categoria): Collection;

    /**
     * Actualiza una regla
     */
    public function update(Regla $regla, array $data): Regla;

    /**
     * Obtiene el valor parseado de una regla por código
     */
    public function getValorByCodigo(string $codigo);

    /**
     * Valida si un valor es válido para una regla
     */
    public function validateValue(Regla $regla, $valor): bool;
}
