<?php

namespace App\Application\Comerciales;

use App\Domain\Contracts\Comerciales\ComercialesServiceInterface;

class ListComercialesUseCase
{
    private $comercialesService;

    public function __construct(ComercialesServiceInterface $comercialesService)
    {
        $this->comercialesService = $comercialesService;
    }

    public function execute()
    {
        return $this->comercialesService->getAll();
    }
}
