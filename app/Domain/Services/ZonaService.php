<?php

namespace App\Domain\Services;

use App\Domain\Contracts\Zonas\ZonaRepositoryInterface;
use App\Domain\Contracts\Zonas\ZonaServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Zona;

class ZonaService implements ZonaServiceInterface
{
    public function __construct(private ZonaRepositoryInterface $repo) {}

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->paginate($filters, $perPage);
    }

    public function create(array $data): Zona
    {
        // Normalizar datos antes de crear
        $data = $this->normalizarDatos($data);
        return $this->repo->create($data);
    }

    public function update(Zona $zona, array $data): Zona
    {
        $data = $this->normalizarDatos($data);
        return $this->repo->update($zona, $data);
    }

    public function delete(Zona $zona): void
    {
        $this->repo->delete($zona);
    }

    public function findById(int $id, bool $failIfNotFound = true): ?Zona
    {
        $zona = $this->repo->findById($id);

        if (!$zona && $failIfNotFound) {
            throw (new ModelNotFoundException())->setModel(Zona::class, [$id]);
        }

        return $zona;
    }

    public function findByCodigo(string $codigo): ?Zona
    {
        return $this->repo->findByCodigo($codigo);
    }

    public function getActivas(): \Illuminate\Support\Collection
    {
        return $this->repo->getActivas();
    }

    /**
     * Normaliza los datos antes de guardar
     */
    private function normalizarDatos(array $data): array
    {
        // Convertir ciudades de string a array si es necesario
        if (isset($data['ciudades']) && is_string($data['ciudades'])) {
            $data['ciudades'] = array_map('trim', explode(',', $data['ciudades']));
        }

        // Normalizar polígono si viene como JSON string
        if (isset($data['poligono']) && is_string($data['poligono'])) {
            $data['poligono'] = json_decode($data['poligono'], true);
        }

        // Generar código automático si no se proporciona
        if (empty($data['codigo']) && isset($data['nombre'])) {
            $data['codigo'] = strtoupper(substr(str_replace(' ', '', $data['nombre']), 0, 10));
        }

        return $data;
    }
}
