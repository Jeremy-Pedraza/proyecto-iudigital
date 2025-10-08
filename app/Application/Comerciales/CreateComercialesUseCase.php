<?php

namespace App\Application\Comerciales;

use App\Domain\Contracts\Comerciales\ComercialesServiceInterface;
use App\Models\Comercial;

class CreateComercialesUseCase
{
    public function __construct(private ComercialesServiceInterface $service) {}

    public function __invoke(array $data): Comercial
    {
        return $this->service->create($data);
    }
}
