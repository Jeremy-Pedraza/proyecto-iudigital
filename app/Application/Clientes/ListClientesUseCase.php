<?php


namespace App\Application\Clientes;

use App\Domain\Contracts\Clientes\ClienteServiceInterface;

class ListClientesUseCase
{
    public function __construct(private ClienteServiceInterface $service) {}
    public function __invoke(array $filters, int $perPage = 15)
    {
        return $this->service->list($filters, $perPage);
    }
}
