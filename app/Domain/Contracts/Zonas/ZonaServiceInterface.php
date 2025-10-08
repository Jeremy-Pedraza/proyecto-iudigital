<?php

namespace App\Domain\Contracts\Zonas;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Zona;

interface ZonaServiceInterface
{
    public function list(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): Zona;
    public function update(Zona $zona, array $data): Zona;
    public function delete(Zona $zona): void;
    public function findById(int $id, bool $failIfNotFound = true): ?Zona;
    public function findByCodigo(string $codigo): ?Zona;
    public function getActivas(): \Illuminate\Support\Collection;
}
