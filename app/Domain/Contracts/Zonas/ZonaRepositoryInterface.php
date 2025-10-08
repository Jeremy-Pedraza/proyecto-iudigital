<?php

namespace App\Domain\Contracts\Zonas;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Zona;

interface ZonaRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): Zona;
    public function update(Zona $zona, array $data): Zona;
    public function delete(Zona $zona): void;
    public function findById(int $id): ?Zona;
    public function findByCodigo(string $codigo): ?Zona;
    public function getActivas(): \Illuminate\Support\Collection;
}
