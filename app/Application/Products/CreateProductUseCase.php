<?php

namespace App\Application\Products;

use App\Domain\Contracts\Products\ProductServiceInterface;
use App\Models\Product;

class CreateProductUseCase
{
    public function __construct(private readonly ProductServiceInterface $svc) {}

    public function handle(array $data): Product
    {
        return $this->svc->create($data);
    }
}
