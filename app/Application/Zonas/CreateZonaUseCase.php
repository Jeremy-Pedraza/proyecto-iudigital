<?php

namespace App\Application\Zonas;

use App\Domain\Contracts\Zonas\ZonaServiceInterface;
use App\Models\Zona;

// ═══════════════════════════════════════════════════════════════════════════════
// CreateZonaUseCase
// ═══════════════════════════════════════════════════════════════════════════════
class CreateZonaUseCase
{
    public function __construct(private ZonaServiceInterface $service) {}

    public function __invoke(array $data): Zona
    {
        return $this->service->create($data);
    }
}
