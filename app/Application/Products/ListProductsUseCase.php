<?php

namespace App\Application\Products;

use App\Domain\Contracts\Products\ProductServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListProductsUseCase
{
    public function __construct(private readonly ProductServiceInterface $svc) {}

    public function handle(
        ?string $query = '',
        ?string $status = null, // 'active'|'inactive'|null
        int $perPage = 10,
        string $sortBy = 'name',
        string $sortDir = 'asc'
    ): LengthAwarePaginator {
        return $this->svc->list($query, $status, $perPage, $sortBy, $sortDir);
    }
}
