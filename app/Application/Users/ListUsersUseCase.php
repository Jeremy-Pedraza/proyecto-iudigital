<?php
//  App/Application/Users/ListUsersUseCase.php 
namespace App\Application\Users;

use App\Domain\Services\UserService;

class ListUsersUseCase
{
    public function __construct(private readonly UserService $svc) {}

    /**
     * Lista usuarios con filtros opcionales y paginación.
     *
     * @param  string|null  $query    Término de búsqueda (nombre, email, etc.)
     * @param  string|null  $role     Filtrar por rol (admin, user, etc.)
     * @param  string|null  $status   Filtrar por estado (active, inactive)
     * @param  int          $perPage  Número de resultados por página.
     *
     * @return mixed
     */
    public function handle(?string $query, ?string $role, ?string $status, int $perPage)
    {
        return $this->svc->list($query, $role, $status, $perPage);
    }
}
