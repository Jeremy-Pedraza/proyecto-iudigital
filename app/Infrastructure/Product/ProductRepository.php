<?php

namespace App\Infrastructure\Products;

use App\Domain\Contracts\Products\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    protected array $allowedSorts = ['name', 'sku', 'price', 'stock', 'created_at'];

    protected function baseQuery(?string $query, ?string $status)
    {
        $qb = Product::query();

        if (!empty($query)) {
            $q = trim($query);
            $qb->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if (in_array($status, ['active', 'inactive'], true)) {
            $qb->where('is_active', $status === 'active');
        }

        return $qb;
    }

    public function paginateFiltered(
        ?string $query,
        ?string $status,
        int $perPage,
        string $sortBy,
        string $sortDir
    ): LengthAwarePaginator {
        $sortBy = in_array($sortBy, $this->allowedSorts, true) ? $sortBy : 'name';
        $sortDir = strtolower($sortDir) === 'desc' ? 'desc' : 'asc';

        return $this->baseQuery($query, $status)
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);
    }

    public function findBy(string $by, int|string $value, array $with = []): Product
    {
        $qb = Product::query();
        if (!empty($with)) $qb->with($with);

        return $qb->where($by, $value)->firstOrFail();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $changes): Product
    {
        $p = Product::findOrFail($id);
        $p->fill($changes)->save();
        return $p;
    }

    public function delete(int $id): void
    {
        Product::findOrFail($id)->delete();
    }
}
