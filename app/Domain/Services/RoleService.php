<?php

namespace App\Domain\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Domain\Contracts\Roles\RoleRepositoryInterface;
use App\Domain\Contracts\Roles\RoleServiceInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Role;

class RoleService implements RoleServiceInterface
{
    public function __construct(
        private readonly RoleRepositoryInterface $repo
    ) {}

    public function list(
        ?string $query,
        ?string $status,
        int $perPage = 10,
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        return $this->repo->paginateFiltered($query, $status, $perPage, $sortBy, $sortDir);
    }

    public function listAll(
        ?string $query,
        ?string $status,
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): Collection {
        return $this->repo->allFiltered($query, $status, $sortBy, $sortDir);
    }

    public function getOne(string $by, int|string $value, array $with = []): Role
    {
        return $this->repo->findBy($by, $value, $with);
    }

    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['name'])) {
                throw ValidationException::withMessages(['name' => 'Nombre requerido']);
            }
            if (empty($data['slug'])) {
                // Si confías en el hook del modelo para generar slug, podrías omitirlo.
                $data['slug'] = Str::slug($data['name']);
            }

            // is_active opcional
            if (!array_key_exists('is_active', $data)) {
                $data['is_active'] = true;
            }

            return $this->repo->create($data);
        });
    }

    public function update(int $id, array $changes): Role
    {
        return DB::transaction(function () use ($id, $changes) {
            if (isset($changes['name']) && !isset($changes['slug'])) {
                // Si cambias el nombre y no envías slug, podríamos recalcularlo (opcional)
                // $changes['slug'] = \Str::slug($changes['name']);
            }
            return $this->repo->update($id, $changes);
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $this->repo->delete($id);
        });
    }
}
