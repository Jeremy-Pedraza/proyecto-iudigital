<?php

namespace App\Application\Rutas;

use App\Domain\Contracts\Rutas\RutaServiceInterface;

/**
 * Caso de uso: Obtener datos necesarios para el formulario de planificación
 */
class GetPlannerDataUseCase
{
    public function __construct(private RutaServiceInterface $service) {}

    /**
     * Obtiene comerciales, ciudades, frecuencias y configuración default
     * para el formulario de planificación de rutas
     */
    public function __invoke(): array
    {
        return $this->service->getDataForPlanner();
    }
}
