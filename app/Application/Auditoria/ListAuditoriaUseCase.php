<?php

namespace App\Application\Auditoria;

use App\Domain\Contracts\Auditoria\AuditoriaServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListAuditoriaUseCase
{
    public function __construct(
        private readonly AuditoriaServiceInterface $service
    ) {}

    public function __invoke(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->service->list($filters, $perPage);
    }
}
