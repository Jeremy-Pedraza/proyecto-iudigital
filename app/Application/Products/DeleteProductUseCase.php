<?php

namespace App\Application\Products;

use App\Domain\Contracts\Products\ProductServiceInterface;

class DeleteProductUseCase
{
    public function __construct(private readonly ProductServiceInterface $svc) {}

    public function handle(int $id): void
    {
        $this->svc->delete($id);
    }
}
