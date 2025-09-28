<?php

namespace App\Domain\Contracts\Products;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Product;

interface ProductServiceInterface
{
    public function list(
        ?string $query,
        ?string $status,
        int $perPage,
        string $sortBy,
        string $sortDir
    ): LengthAwarePaginator;

    public function getOne(string $by, int|string $value, array $with = []): Product;

    public function create(array $data): Product;

    public function update(int $id, array $changes): Product;

    public function delete(int $id): void;
}
