<?php

namespace App\Application\Zonas;

use App\Domain\Contracts\Zonas\ZonaServiceInterface;
use App\Models\Zona;

// ═══════════════════════════════════════════════════════════════════════════════
// GetZonasActivasUseCase
// ═══════════════════════════════════════════════════════════════════════════════
class GetZonasActivasUseCase
{
    public function __construct(private ZonaServiceInterface $service) {}

    public function __invoke(): \Illuminate\Support\Collection
    {
        return $this->service->getActivas();
    }
}
