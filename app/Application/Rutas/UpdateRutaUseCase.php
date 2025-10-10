<?php

namespace App\Application\Rutas;

use App\Domain\Contracts\Rutas\RutaServiceInterface;
use App\Models\Ruta;

class UpdateRutaUseCase
{
    public function __construct(private RutaServiceInterface $service) {}

    public function __invoke(Ruta $ruta, array $data)
    {
        return $this->service->update($ruta, $data);
    }
}
