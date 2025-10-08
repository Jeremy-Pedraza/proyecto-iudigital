<?php

namespace App\Application\Comerciales;

use App\Domain\Contracts\Comerciales\ComercialesServiceInterface;
use App\Models\Comercial;

class UpdateComercialesUseCase
{
    public function __construct(private ComercialesServiceInterface $service) {}

    public function __invoke(Comercial $comercial, array $data): Comercial
    {
        return $this->service->update($comercial, $data);
    }
}
