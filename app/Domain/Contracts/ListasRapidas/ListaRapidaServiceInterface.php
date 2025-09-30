<?php

namespace App\Domain\Contracts\ListasRapidas;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\ListaRapida;

interface ListaRapidaServiceInterface
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function get(int $id): ListaRapida;
    public function create(array $data): ListaRapida;
    public function update(int $id, array $data): ListaRapida;
    public function delete(int $id): void;
}
