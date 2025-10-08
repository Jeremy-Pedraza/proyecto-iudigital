<?php

namespace App\Application\Zonas;

use App\Domain\Contracts\Zonas\ZonaServiceInterface;
use App\Models\Zona;

// ═══════════════════════════════════════════════════════════════════════════════
// DeleteZonaUseCase
// ═══════════════════════════════════════════════════════════════════════════════
class DeleteZonaUseCase
{
    public function __construct(private ZonaServiceInterface $service) {}

    public function __invoke(Zona $zona): void
    {
        $this->service->delete($zona);
    }
}
