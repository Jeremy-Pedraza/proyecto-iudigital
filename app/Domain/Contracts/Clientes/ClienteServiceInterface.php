<?php
// app/Domain/Clientes/Services/ClienteService.php
namespace App\Domain\Contracts\Clientes;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Cliente;

interface ClienteServiceInterface
{
    public function list(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): Cliente;
    public function update(Cliente $cliente, array $data): Cliente;
    public function delete(Cliente $cliente): void;

    public function findById(int $id, array $with = [], bool $failIfNotFound = true): ?Cliente;
}
