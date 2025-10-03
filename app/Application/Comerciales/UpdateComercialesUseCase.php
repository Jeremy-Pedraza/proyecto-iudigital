<?php

namespace App\Application\Comerciales;

use App\Domain\Contracts\Comerciales\ComercialesServiceInterface;

class UpdateComercialesUseCase
{
    private $comercialesService;

    public function __construct(ComercialesServiceInterface $comercialesService)
    {
        $this->comercialesService = $comercialesService;
    }

    public function execute(string $id, array $data)
    {
        return $this->comercialesService->update($id, $data);
    }
}
