<?php

namespace App\Infrastructure\Reglas;

use App\Domain\Contracts\Reglas\ReglaRepositoryInterface;
use App\Models\Regla;
use Illuminate\Support\Collection;

class ReglaRepository implements ReglaRepositoryInterface
{
    public function getAllGroupedByCategory(): Collection
    {
        return Regla::activas()
            ->ordenadas()
            ->get()
            ->groupBy('categoria');
    }

    public function getAll(): Collection
    {
        return Regla::ordenadas()->get();
    }

    public function getByCategory(string $categoria): Collection
    {
        return Regla::categoria($categoria)
            ->activas()
            ->ordenadas()
            ->get();
    }

    public function findByCodigo(string $codigo): ?Regla
    {
        return Regla::where('codigo', $codigo)->first();
    }

    public function update(Regla $regla, array $data): Regla
    {
        $regla->update($data);
        return $regla->fresh();
    }

    public function getActive(): Collection
    {
        return Regla::activas()->ordenadas()->get();
    }

    public function getEditable(): Collection
    {
        return Regla::editables()->ordenadas()->get();
    }
}
