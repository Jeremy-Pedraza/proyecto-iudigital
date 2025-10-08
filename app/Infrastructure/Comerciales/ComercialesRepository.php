<?php

namespace App\Infrastructure\Comerciales;

use App\Domain\Contracts\Comerciales\ComercialesRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Comercial;

class ComercialesRepository implements ComercialesRepositoryInterface
{
    public function paginate(array $f, int $perPage = 15): LengthAwarePaginator
    {
        return Comercial::query()
            ->with(['user:id,name', 'zona:id,nombre'])
            ->search($f['search'] ?? null)
            ->estado($f['estado'] ?? null)
            ->zona($f['zona_id'] ?? null)
            ->sort($f['sort_by'] ?? null, $f['sort_dir'] ?? null)
            ->paginate($perPage);
    }

    public function create(array $data): Comercial
    {
        return Comercial::create($data);
    }

    public function update(Comercial $comercial, array $data): Comercial
    {
        $comercial->update($data);
        return $comercial;
    }

    public function delete(Comercial $comercial): void
    {
        $comercial->delete();
    }

    public function findById(int $id, array $with = []): ?Comercial
    {
        $q = Comercial::query();
        if (!empty($with)) {
            $q->with($with);
        }
        return $q->find($id);
    }

    public function getAllActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Comercial::where('estado', 'activo')->get();
    }
}
