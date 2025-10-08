<?php

namespace App\Application\Zonas;

use App\Domain\Contracts\Zonas\ZonaServiceInterface;
use App\Models\Zona;

// ═══════════════════════════════════════════════════════════════════════════════
// UpdateZonaUseCase
// ═══════════════════════════════════════════════════════════════════════════════
class UpdateZonaUseCase
{
    public function __construct(private ZonaServiceInterface $service) {}

    public function __invoke(Zona $zona, array $data): Zona
    {
        return $this->service->update($zona, $data);
    }
}
