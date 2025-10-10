<?php

namespace App\Application\Rutas;

use App\Domain\Contracts\Rutas\RutaServiceInterface;

class ListRutasUseCase
{
    public function __construct(private RutaServiceInterface $service) {}

    public function __invoke(array $filters, int $perPage = 15)
    {
        return $this->service->list($filters, $perPage);
    }
}
