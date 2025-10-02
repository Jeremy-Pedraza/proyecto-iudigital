<?php

namespace App\Infrastructure\Clientes;

use App\Domain\Contracts\Clientes\ClienteRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Cliente;

class ClienteRepository implements ClienteRepositoryInterface
{
    public function paginate(array $f, int $perPage = 15): LengthAwarePaginator
    {
        return Cliente::query()
            ->with('comercial:id,name')
            ->search($f['search'] ?? null)
            ->ciudad($f['ciudad'] ?? null)
            ->estado($f['estado'] ?? null)
            ->comercial($f['comercial_id'] ?? null)
            ->frecuencia($f['frecuencia'] ?? null)
            ->sort($f['sort_by'] ?? null, $f['sort_dir'] ?? null)
            ->paginate($perPage);
    }
    public function create(array $data): Cliente
    {
        return Cliente::create($data);
    }
    public function update(Cliente $cliente, array $data): Cliente
    {
        $cliente->update($data);
        return $cliente;
    }
    public function delete(Cliente $cliente): void
    {
        $cliente->delete();
    }
    public function findById(int $id, array $with = []): ?Cliente
    {
        $q = Cliente::query();
        if (!empty($with)) {
            $q->with($with);
        }
        return $q->find($id);
    }
}
