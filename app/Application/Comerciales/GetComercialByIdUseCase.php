<?php

namespace App\Application\Comerciales;

use App\Domain\Contracts\Comerciales\ComercialesServiceInterface;
use App\Models\Comercial;

class GetComercialByIdUseCase
{
    public function __construct(private ComercialesServiceInterface $service) {}

    public function __invoke(int $id, array $with = []): ?Comercial
    {
        return $this->service->findById($id, $with, true);
    }
}
