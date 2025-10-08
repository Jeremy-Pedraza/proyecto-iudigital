<?php

namespace App\Application\Zonas;

use App\Domain\Contracts\Zonas\ZonaServiceInterface;
use App\Models\Zona;

// ═══════════════════════════════════════════════════════════════════════════════
// ListZonasUseCase
// ═══════════════════════════════════════════════════════════════════════════════
class ListZonasUseCase
{
    public function __construct(private ZonaServiceInterface $service) {}

    public function __invoke(array $filters, int $perPage = 15)
    {
        return $this->service->list($filters, $perPage);
    }
}
