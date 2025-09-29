<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Genera 50 productos con datos válidos
        Product::factory()->count(50)->create();
    }
}
