<?php

namespace App\Application\Roles;

use App\Domain\Contracts\Roles\RoleServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * ListRolesUseCase
 *
 * Lista roles con búsqueda, orden y paginación.
 * Depende del servicio de dominio (RoleServiceInterface).
 */
class ListRolesUseCase
{
    public function __construct(private readonly RoleServiceInterface $svc) {}

    /**
     * @param string|null $query   Texto a buscar (name|slug|description)
     * @param int         $perPage Elementos por página (>0 pagina; <=0 no soportado en este caso de uso)
     * @param string      $sortBy  name|slug|created_at
     * @param string      $sortDir asc|desc
     * @param string|null $status  'active'|'inactive'|null (si existe columna is_active)
     *
     * @return LengthAwarePaginator
     */
    public function handle(
        ?string $query = '',
        int $perPage = 10,
        string $sortBy = 'name',
        string $sortDir = 'asc',
        ?string $status = null
    ): LengthAwarePaginator {
        return $this->svc->list($query, $status, $perPage, $sortBy, $sortDir);
    }
}
