<?php

namespace App\Application\Products;

use App\Domain\Contracts\Products\ProductServiceInterface;
use App\Models\Product;

class GetProductUseCase
{
    public function __construct(private readonly ProductServiceInterface $svc) {}

    public function handle(string $by, int|string $value, array $with = []): Product
    {
        return $this->svc->getOne($by, $value, $with);
    }
}
