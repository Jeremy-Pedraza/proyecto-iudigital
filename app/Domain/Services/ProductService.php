<?php

namespace App\Domain\Services;

use App\Domain\Contracts\Products\ProductRepositoryInterface;
use App\Domain\Contracts\Products\ProductServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Product;

class ProductService implements ProductServiceInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $repo
    ) {}

    public function list(
        ?string $query,
        ?string $status,
        int $perPage = 10,
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        return $this->repo->paginateFiltered($query, $status, $perPage, $sortBy, $sortDir);
    }

    public function getOne(string $by, int|string $value, array $with = []): Product
    {
        return $this->repo->findBy($by, $value, $with);
    }

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['name'])) throw ValidationException::withMessages(['name' => 'Nombre requerido']);
            if (empty($data['sku']))  throw ValidationException::withMessages(['sku'  => 'SKU requerido']);

            $data['price']     = $data['price']     ?? 0;
            $data['stock']     = $data['stock']     ?? 0;
            $data['is_active'] = array_key_exists('is_active', $data) ? (bool)$data['is_active'] : true;

            return $this->repo->create($data);
        });
    }

    public function update(int $id, array $changes): Product
    {
        return DB::transaction(fn() => $this->repo->update($id, $changes));
    }

    public function delete(int $id): void
    {
        DB::transaction(fn() => $this->repo->delete($id));
    }
}
