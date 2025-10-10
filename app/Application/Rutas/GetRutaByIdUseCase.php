<?php

namespace App\Application\Rutas;

use App\Domain\Contracts\Rutas\RutaServiceInterface;
use App\Models\Ruta;

class GetRutaByIdUseCase
{
    public function __construct(private RutaServiceInterface $service) {}

    /**
     * @param int $id           ID de la ruta a obtener
     * @param array $with       Relaciones a cargar (e.g. ['comercial:id,name', 'paradas'])
     * @param bool $failIfNotFound  true => lanza 404 (ModelNotFoundException)
     */
    public function __invoke(int $id, array $with = ['comercial:id,name', 'paradas.cliente'], bool $failIfNotFound = true): ?Ruta
    {
        return $this->service->findById($id, $with, $failIfNotFound);
    }
}
