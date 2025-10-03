<?php

namespace App\Domain\Services;

use App\Domain\Contracts\Comerciales\ComercialesServiceInterface;
use App\Domain\Contracts\Comerciales\ComercialesRepositoryInterface;

class ComercialesService implements ComercialesServiceInterface
{
    private $comercialesRepository;

    public function __construct(ComercialesRepositoryInterface $comercialesRepository)
    {
        $this->comercialesRepository = $comercialesRepository;
    }

    public function getAll()
    {
        return $this->comercialesRepository->getAll();
    }

    public function findById(string $id)
    {
        $comercial = $this->comercialesRepository->findById($id);

        if (!$comercial) {
            throw new \Exception('Comercial no encontrado');
        }

        return $comercial;
    }

    public function create(array $data)
    {

        return $this->comercialesRepository->create($data);
    }

    public function update(string $id, array $data)
    {
        // Verificar que el comercial existe
        $this->findById($id);

        // Validar datos
        $this->validateComercialData($data, $id);

        return $this->comercialesRepository->update($id, $data);
    }

    public function delete(string $id)
    {
        // Verificar que el comercial existe
        $this->findById($id);

        return $this->comercialesRepository->delete($id);
    }

    private function validateComercialData(array $data, string $excludeId = null)
    {
        // Aquí puedes agregar validaciones de negocio específicas
        if (empty($data['nombre'])) {
            throw new \Exception('El nombre es requerido');
        }

        if (empty($data['email'])) {
            throw new \Exception('El email es requerido');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('El email no tiene un formato válido');
        }
    }
}
