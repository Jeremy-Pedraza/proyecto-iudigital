<?php

namespace App\Domain\Contracts\Comerciales;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Comercial;

interface ComercialesRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): Comercial;
    public function update(Comercial $comercial, array $data): Comercial;
    public function delete(Comercial $comercial): void;
    public function findById(int $id, array $with = []): ?Comercial;
    public function getAllActive(): \Illuminate\Database\Eloquent\Collection;
}
