<?php

namespace App\Domain\Contracts\ListasRapidas;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\ListaRapida;

interface ListaRapidaRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?ListaRapida;
    public function create(array $data): ListaRapida;
    public function update(int $id, array $data): ListaRapida;
    public function delete(int $id): void;
    public function existsByGrupoAndClave(string $grupo, string $clave, ?int $exceptId = null): bool;
}
