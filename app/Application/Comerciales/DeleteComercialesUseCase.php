<?php

namespace App\Application\Comerciales;

use App\Domain\Contracts\Comerciales\ComercialesServiceInterface;
use App\Models\Comercial;

class DeleteComercialesUseCase
{
    public function __construct(private ComercialesServiceInterface $service) {}

    public function __invoke(Comercial $comercial): void
    {
        $this->service->delete($comercial);
    }
}
