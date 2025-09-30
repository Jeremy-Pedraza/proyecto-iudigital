<?php

namespace App\Domain\Services;

use App\Domain\Contracts\Auditoria\AuditoriaServiceInterface;
use App\Domain\Contracts\Auditoria\AuditoriaRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuditoriaService implements AuditoriaServiceInterface
{
    public function __construct(
        private readonly AuditoriaRepositoryInterface $repository
    ) {}

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateWithFilters($filters, $perPage);
    }
}
