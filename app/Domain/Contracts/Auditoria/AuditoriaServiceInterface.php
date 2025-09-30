<?php

namespace App\Domain\Contracts\Auditoria;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AuditoriaServiceInterface
{
    public function list(array $filters, int $perPage = 15): LengthAwarePaginator;
}
