<?php
// App/Application/Users/CreateUserUseCase.php
namespace App\Application\Users;

use App\Domain\Services\UserService;

class CreateUserUseCase
{
    public function __construct(private readonly UserService $svc) {}

    /**
     * Crea un nuevo usuario.
     *
     * @param  array  $data  Datos del usuario (nombre, email, password, roles, etc.)
     *
     * @return mixed
     */
    public function handle(array $data)
    {
        return $this->svc->create($data);
    }
}
