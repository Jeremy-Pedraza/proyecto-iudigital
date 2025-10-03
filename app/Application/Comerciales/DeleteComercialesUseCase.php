<?php

namespace App\Application\Comerciales;

use App\Domain\Contracts\Comerciales\ComercialesServiceInterface;

class DeleteComercialesUseCase
{
    private $comercialesService;

    public function __construct(ComercialesServiceInterface $comercialesService)
    {
        $this->comercialesService = $comercialesService;
    }

    public function execute(string $id)
    {
        return $this->comercialesService->delete($id);
    }
}
