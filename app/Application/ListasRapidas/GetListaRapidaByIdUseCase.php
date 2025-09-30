<?php

namespace App\Application\ListasRapidas;

use App\Domain\Contracts\ListasRapidas\ListaRapidaServiceInterface;
use App\Models\ListaRapida;

class GetListaRapidaByIdUseCase
{
    public function __construct(private ListaRapidaServiceInterface $service) {}

    public function __invoke(int $id): ListaRapida
    {
        return $this->service->get($id);
    }
}
