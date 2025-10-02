<?php

namespace App\Application\Clientes;

use App\Domain\Contracts\Clientes\ClienteServiceInterface;
use App\Models\Cliente;

class GetClienteByIdUseCase
{
    public function __construct(private ClienteServiceInterface $service) {}

    /**
     * @param int $id           ID del cliente a obtener
     * @param array $with       Relaciones a cargar (e.g. ['comercial:id,name'])
     * @param bool $failIfNotFound  true => lanza 404 (ModelNotFoundException)
     */
    public function __invoke(int $id, array $with = ['comercial:id,name'], bool $failIfNotFound = true): ?Cliente
    {
        return $this->service->findById($id, $with, $failIfNotFound);
    }
}
