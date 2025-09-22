<?php

namespace App\Domain\Contracts\Users;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;

interface UserRepositoryInterface
{
    public function paginateFiltered(?string $query, ?string $role, ?string $status, int $perPage = 10): LengthAwarePaginator;

    public function findBy(string $by, int|string $value, array $with = []): User;

    public function create(array $data): User;

    public function update(int $id, array $changes): User;

    public function delete(int $id): void;
}
