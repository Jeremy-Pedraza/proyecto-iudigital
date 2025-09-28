<?php

namespace App\Application\Products;

use App\Domain\Contracts\Products\ProductServiceInterface;
use App\Models\Product;

class UpdateProductUseCase
{
    public function __construct(private readonly ProductServiceInterface $svc) {}

    public function handle(int $id, array $changes): Product
    {
        return $this->svc->update($id, $changes);
    }
}
