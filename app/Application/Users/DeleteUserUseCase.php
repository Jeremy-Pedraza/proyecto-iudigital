<?php
// App/Application/Users/DeleteUserUseCase.php
namespace App\Application\Users;

use App\Domain\Services\UserService;

class DeleteUserUseCase
{
    public function __construct(private readonly UserService $svc) {}


    /**
     * Elimina un usuario por su ID.
     *
     * @param  int  $id  ID del usuario a eliminar.
     *
     * @return void
     */
    public function handle(int $id): void
    {
        $this->svc->delete($id);
    }
}
