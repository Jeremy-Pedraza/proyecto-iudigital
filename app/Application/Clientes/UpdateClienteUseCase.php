<?php

namespace App\Application\Clientes;

use App\Domain\Contracts\Clientes\ClienteServiceInterface;
use App\Models\Cliente;

class UpdateClienteUseCase
{
    public function __construct(private ClienteServiceInterface $service) {}
    public function __invoke(Cliente $cliente, array $data)
    {
        return $this->service->update($cliente, $data);
    }
}
