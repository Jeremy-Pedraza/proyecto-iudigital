<?php

namespace App\Application\Comerciales;

use App\Domain\Contracts\Comerciales\ComercialesServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListComercialesUseCase
{
    public function __construct(private ComercialesServiceInterface $service) {}

    public function __invoke(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->service->list($filters, $perPage);
    }
}
