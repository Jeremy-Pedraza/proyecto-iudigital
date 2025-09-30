<?php
// app/Application/ListasRapidas/UseCases/UpdateListaRapidaUseCase.php

namespace App\Application\ListasRapidas;

use App\Domain\Contracts\ListasRapidas\ListaRapidaServiceInterface;
use App\Models\ListaRapida;

class UpdateListaRapidaUseCase
{
    public function __construct(private ListaRapidaServiceInterface $service) {}

    public function __invoke(int $id, array $data): ListaRapida
    {
        return $this->service->update($id, $data);
    }
}
