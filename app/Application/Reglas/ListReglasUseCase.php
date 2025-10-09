<?php

namespace App\Application\Reglas;

use App\Domain\Contracts\Reglas\ReglaServiceInterface;
use Illuminate\Support\Collection;

class ListReglasUseCase
{
    public function __construct(private ReglaServiceInterface $service) {}

    /**
     * Ejecuta el caso de uso
     * 
     * @param bool $groupByCategory Si es true, agrupa por categoría
     * @return Collection
     */
    public function __invoke(bool $groupByCategory = true): Collection
    {
        if ($groupByCategory) {
            return $this->service->listGroupedByCategory();
        }

        return $this->service->list();
    }
}
