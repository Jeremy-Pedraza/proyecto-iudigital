<?php
// app/Application/ListasRapidas/UseCases/ListListasRapidasUseCase.php

namespace App\Application\ListasRapidas;

use App\Domain\Contracts\ListasRapidas\ListaRapidaServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListListasRapidasUseCase
{
    public function __construct(private ListaRapidaServiceInterface $service) {}

    public function __invoke(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->service->list($filters, $perPage);
    }
}
