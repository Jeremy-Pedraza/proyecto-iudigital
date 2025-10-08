<?php

namespace App\Application\Zonas;

use App\Domain\Contracts\Zonas\ZonaServiceInterface;
use App\Models\Zona;

// ═══════════════════════════════════════════════════════════════════════════════
// GetZonaByIdUseCase
// ═══════════════════════════════════════════════════════════════════════════════
class GetZonaByIdUseCase
{
    public function __construct(private ZonaServiceInterface $service) {}

    public function __invoke(int $id, bool $failIfNotFound = true): ?Zona
    {
        return $this->service->findById($id, $failIfNotFound);
    }
}
