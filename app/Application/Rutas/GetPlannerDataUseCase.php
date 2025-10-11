<?php

namespace App\Application\Rutas;

use App\Domain\Contracts\Rutas\RutaServiceInterface;

class GetPlannerDataUseCase
{
    public function __construct(private RutaServiceInterface $service) {}

    /**
     * Obtiene todos los datos necesarios para el formulario de planificación
     */
    public function __invoke(): array
    {
        return $this->service->getDataForPlanner();
    }
}
