<?php
// App/Application/Users/UpdateUserUseCase.php
namespace App\Application\Users;

use App\Domain\Services\UserService;

class UpdateUserUseCase
{
    public function __construct(private readonly UserService $svc) {}

    /**
     * Actualiza un usuario existente.
     *
     * @param  int    $id       ID del usuario a actualizar.
     * @param  array  $changes  Cambios a aplicar (nombre, email, password, roles, etc.)
     *
     * @return mixed
     */
    public function handle(int $id, array $changes)
    {
        return $this->svc->update($id, $changes);
    }
}
