<?php

namespace App\Domain\Contracts\Roles;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\Role;

interface RoleRepositoryInterface
{
    public function paginateFiltered(
        ?string $query,
        ?string $status,
        int $perPage = 10,
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator;

    public function allFiltered(
        ?string $query,
        ?string $status,
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): Collection;

    public function findBy(string $by, int|string $value, array $with = []): Role;

    public function create(array $data): Role;

    public function update(int $id, array $changes): Role;

    public function delete(int $id): void;
}
