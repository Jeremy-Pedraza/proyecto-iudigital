<?php

namespace App\Domain\Services;

use App\Domain\Contracts\Clientes\ClienteRepositoryInterface;
use App\Domain\Contracts\Clientes\ClienteServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Cliente;

class ClienteService implements ClienteServiceInterface
{
    public function __construct(private ClienteRepositoryInterface $repo) {}

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        // Aquí podrías agregar lógica de negocio cross-cutting:
        // normalizar filtros, policies extra, caching, etc.
        return $this->repo->paginate($filters, $perPage);
    }

    public function create(array $data): Cliente
    {
        // Lógica previa, por ejemplo: sanitización, defaults, eventos de dominio
        return $this->repo->create($data);
    }

    public function update(Cliente $cliente, array $data): Cliente
    {
        return $this->repo->update($cliente, $data);
    }

    public function delete(Cliente $cliente): void
    {
        $this->repo->delete($cliente);
    }

    public function findById(int $id, array $with = [], bool $failIfNotFound = true): ?Cliente
    {
        $cliente = $this->repo->findById($id, $with);
        if (!$cliente && $failIfNotFound) {
            throw (new ModelNotFoundException())->setModel(Cliente::class, [$id]);
        }
        return $cliente;
    }
}
