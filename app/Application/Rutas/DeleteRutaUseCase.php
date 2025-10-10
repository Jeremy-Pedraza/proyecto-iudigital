<?php

namespace App\Application\Rutas;

use App\Domain\Contracts\Rutas\RutaServiceInterface;
use App\Models\Ruta;

class DeleteRutaUseCase
{
    public function __construct(private RutaServiceInterface $service) {}

    public function __invoke(Ruta $ruta): void
    {
        $this->service->delete($ruta);
    }
}
