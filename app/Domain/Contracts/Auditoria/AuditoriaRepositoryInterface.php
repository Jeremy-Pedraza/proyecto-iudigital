<?php

namespace App\Domain\Contracts\Auditoria;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AuditoriaRepositoryInterface
{
    /**
     * Lista paginada con filtros.
     *
     * Filtros soportados:
     *  - search (en description)
     *  - module
     *  - action
     *  - user_id
     *  - date_from (Y-m-d)
     *  - date_to   (Y-m-d)
     */
    public function paginateWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator;
}
