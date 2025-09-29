<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<\App\Models\Product> */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        // Precio de venta y costo (costo < precio)
        $price = $this->faker->randomFloat(2, 5, 1500); // 5.00 - 1500.00
        $cost  = round($price * $this->faker->numberBetween(60, 90) / 100, 2); // 60% - 90% del precio

        return [
            'name'        => ucfirst($this->faker->unique()->words(3, true)), // "Smart Widget Pro"
            'sku'         => $this->faker->unique()->bothify('PRD-########'), // PRD-12345678
            'description' => $this->faker->optional(0.7)->paragraph(),
            'price'       => $price,
            'cost'        => $cost,
            'stock'       => $this->faker->numberBetween(0, 500),
            'is_active'   => $this->faker->boolean(85),
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    }
}
