<?php

namespace App\Application\Clientes;

use App\Domain\Contracts\Clientes\ClienteServiceInterface;

class CreateClienteUseCase
{
    public function __construct(private ClienteServiceInterface $service) {}
    public function __invoke(array $data)
    {
        return $this->service->create($data);
    }
}
