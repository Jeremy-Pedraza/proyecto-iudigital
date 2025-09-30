<?php

namespace App\Application\ListasRapidas;

use App\Domain\Contracts\ListasRapidas\ListaRapidaServiceInterface;

class DeleteListaRapidaUseCase
{
    public function __construct(private ListaRapidaServiceInterface $service) {}

    public function __invoke(int $id): void
    {
        $this->service->delete($id);
    }
}
