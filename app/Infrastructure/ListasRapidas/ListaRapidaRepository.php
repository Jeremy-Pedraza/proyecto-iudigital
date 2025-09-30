<?php
// app/Infrastructure/Persistence/Eloquent/Repositories/EloquentListaRapidaRepository.php

namespace App\Infrastructure\ListasRapidas;

use App\Domain\Contracts\ListasRapidas\ListaRapidaRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\ListaRapida;

class ListaRapidaRepository implements ListaRapidaRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $q = ListaRapida::query();

        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $q->where(function ($w) use ($s) {
                $w->where('grupo', 'like', "%{$s}%")
                    ->orWhere('clave', 'like', "%{$s}%")
                    ->orWhere('valor', 'like', "%{$s}%");
            });
        }

        if (isset($filters['grupo']) && $filters['grupo'] !== '') {
            $q->where('grupo', $filters['grupo']);
        }

        if (isset($filters['estado']) && $filters['estado'] !== '') {
            $q->where('estado', (bool)$filters['estado']);
        }

        // Orden
        $sortBy = $filters['sortBy'] ?? 'grupo';
        $sortDir = $filters['sortDir'] ?? 'asc';
        $q->orderBy($sortBy, $sortDir);

        return $q->paginate($perPage);
    }

    public function findById(int $id): ?ListaRapida
    {
        return ListaRapida::find($id);
    }

    public function create(array $data): ListaRapida
    {
        return ListaRapida::create($data);
    }

    public function update(int $id, array $data): ListaRapida
    {
        $m = $this->findById($id);
        if (!$m) abort(404);
        $m->update($data);
        return $m;
    }

    public function delete(int $id): void
    {
        $m = $this->findById($id);
        if (!$m) abort(404);
        $m->delete();
    }

    public function existsByGrupoAndClave(string $grupo, string $clave, ?int $exceptId = null): bool
    {
        $q = ListaRapida::where('grupo', $grupo)->where('clave', $clave);
        if ($exceptId) $q->where('id', '!=', $exceptId);
        return $q->exists();
    }
}
