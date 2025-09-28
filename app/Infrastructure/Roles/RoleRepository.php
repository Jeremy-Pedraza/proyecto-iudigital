<?php

namespace App\Infrastructure\Roles;

use App\Domain\Contracts\Roles\RoleRepositoryInterface;
use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Schema;

class RoleRepository implements RoleRepositoryInterface
{
    protected array $allowedSorts = ['name', 'slug', 'created_at'];

    protected function baseQuery(?string $query, ?string $status)
    {
        $builder = Role::query();

        if ($query !== null && trim($query) !== '') {
            $q = trim($query);
            $builder->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // Filtro por estado solo si existe la columna is_active
        if (Schema::hasColumn('roles', 'is_active') && in_array($status, ['active', 'inactive'], true)) {
            $builder->where('is_active', $status === 'active');
        }

        return $builder;
    }

    public function paginateFiltered(
        ?string $query,
        ?string $status,
        int $perPage = 10,
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        $sortBy = in_array($sortBy, $this->allowedSorts, true) ? $sortBy : 'name';
        $sortDir = strtolower($sortDir) === 'desc' ? 'desc' : 'asc';

        return $this->baseQuery($query, $status)
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);
    }

    public function allFiltered(
        ?string $query,
        ?string $status,
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): Collection {
        $sortBy = in_array($sortBy, $this->allowedSorts, true) ? $sortBy : 'name';
        $sortDir = strtolower($sortDir) === 'desc' ? 'desc' : 'asc';

        return $this->baseQuery($query, $status)
            ->orderBy($sortBy, $sortDir)
            ->get();
    }

    public function findBy(string $by, int|string $value, array $with = []): Role
    {
        $builder = Role::query();
        if (!empty($with)) {
            $builder->with($with);
        }

        $role = $builder->where($by, $value)->first();

        if (!$role) {
            throw (new ModelNotFoundException())->setModel(Role::class, [$value]);
        }

        return $role;
    }

    public function create(array $data): Role
    {
        /** @var Role $role */
        $role = Role::create($data);
        return $role;
    }

    public function update(int $id, array $changes): Role
    {
        $role = Role::findOrFail($id);
        $role->fill($changes);
        $role->save();

        return $role;
    }

    public function delete(int $id): void
    {
        $role = Role::findOrFail($id);
        $role->delete();
    }
}
