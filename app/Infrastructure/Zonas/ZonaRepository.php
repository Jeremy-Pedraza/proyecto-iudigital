<?php

namespace App\Infrastructure\Zonas;

use App\Domain\Contracts\Zonas\ZonaRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Zona;

class ZonaRepository implements ZonaRepositoryInterface
{
    public function paginate(array $f, int $perPage = 15): LengthAwarePaginator
    {
        return Zona::query()
            ->search($f['search'] ?? null)
            ->estado($f['estado'] ?? null)
            ->ciudad($f['ciudad'] ?? null)
            ->sort($f['sort_by'] ?? null, $f['sort_dir'] ?? null)
            ->paginate($perPage);
    }

    public function create(array $data): Zona
    {
        return Zona::create($data);
    }

    public function update(Zona $zona, array $data): Zona
    {
        $zona->update($data);
        return $zona->fresh();
    }

    public function delete(Zona $zona): void
    {
        $zona->delete();
    }

    public function findById(int $id): ?Zona
    {
        return Zona::find($id);
    }

    public function findByCodigo(string $codigo): ?Zona
    {
        return Zona::where('codigo', $codigo)->first();
    }

    public function getActivas(): \Illuminate\Support\Collection
    {
        return Zona::where('estado', 'activa')
            ->orderBy('nombre')
            ->get();
    }
}
