<?php

namespace App\Domain\Services;

use App\Domain\Contracts\Comerciales\ComercialesRepositoryInterface;
use App\Domain\Contracts\Comerciales\ComercialesServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Comercial;

class ComercialesService implements ComercialesServiceInterface
{
    public function __construct(private ComercialesRepositoryInterface $repo) {}

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        // Lógica de negocio: normalizar filtros, caching, etc.
        return $this->repo->paginate($filters, $perPage);
    }

    public function create(array $data): Comercial
    {
        // Lógica previa: validaciones de negocio, defaults
        $data['estado'] = $data['estado'] ?? 'activo';
        $data['capacidad_paradas_dia'] = $data['capacidad_paradas_dia'] ?? 10;

        return $this->repo->create($data);
    }

    public function update(Comercial $comercial, array $data): Comercial
    {
        // Lógica previa: validaciones, eventos
        return $this->repo->update($comercial, $data);
    }

    public function delete(Comercial $comercial): void
    {
        // Verificar si tiene clientes asignados
        if ($comercial->clientes()->count() > 0) {
            throw new \Exception('No se puede eliminar un comercial con clientes asignados.');
        }

        $this->repo->delete($comercial);
    }

    public function findById(int $id, array $with = [], bool $failIfNotFound = true): ?Comercial
    {
        $comercial = $this->repo->findById($id, $with);

        if (!$comercial && $failIfNotFound) {
            throw (new ModelNotFoundException())->setModel(Comercial::class, [$id]);
        }

        return $comercial;
    }

    public function getAllActive(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repo->getAllActive();
    }
}
