<?php

namespace App\Domain\Services;

use App\Domain\Contracts\ListasRapidas\ListaRapidaRepositoryInterface;
use App\Domain\Contracts\ListasRapidas\ListaRapidaServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use App\Models\ListaRapida;

class ListaRapidaService implements ListaRapidaServiceInterface
{
    public function __construct(private ListaRapidaRepositoryInterface $repo) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->paginate($filters, $perPage);
    }

    public function get(int $id): ListaRapida
    {
        $m = $this->repo->findById($id);
        if (!$m) abort(404);
        return $m;
    }

    public function create(array $data): ListaRapida
    {
        $this->validateUniqueClave($data['grupo'], $data['clave']);
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): ListaRapida
    {
        $this->validateUniqueClave($data['grupo'], $data['clave'], $id);
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }

    private function validateUniqueClave(string $grupo, string $clave, ?int $exceptId = null): void
    {
        if ($this->repo->existsByGrupoAndClave($grupo, $clave, $exceptId)) {
            throw ValidationException::withMessages([
                'clave' => "La clave '{$clave}' ya existe en el grupo '{$grupo}'."
            ]);
        }
    }
}
