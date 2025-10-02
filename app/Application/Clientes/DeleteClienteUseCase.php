<?php

namespace App\Application\Clientes;

use App\Domain\Contracts\Clientes\ClienteServiceInterface;

use App\Models\Cliente;


class DeleteClienteUseCase
{
    public function __construct(private ClienteServiceInterface $service) {}
    public function __invoke(Cliente $cliente): void
    {
        $this->service->delete($cliente);
    }
}
