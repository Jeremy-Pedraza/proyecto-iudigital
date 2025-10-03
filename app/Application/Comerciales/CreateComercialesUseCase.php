<?php

namespace App\Application\Comerciales;

use App\Domain\Contracts\Comerciales\ComercialesServiceInterface;

class CreateComercialesUseCase
{
    private $comercialesService;

    public function __construct(ComercialesServiceInterface $comercialesService)
    {
        $this->comercialesService = $comercialesService;
    }

    public function execute(array $data)
    {
        return $this->comercialesService->create($data);
    }
}
