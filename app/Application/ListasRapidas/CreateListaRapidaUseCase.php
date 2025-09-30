<?php

namespace App\Application\ListasRapidas;

use App\Domain\Contracts\ListasRapidas\ListaRapidaServiceInterface;
use App\Models\ListaRapida;

class CreateListaRapidaUseCase
{
    public function __construct(private ListaRapidaServiceInterface $service) {}

    public function __invoke(array $data): ListaRapida
    {
        return $this->service->create($data);
    }
}
