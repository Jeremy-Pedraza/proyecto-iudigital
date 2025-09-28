<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'cost',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'cost'      => 'decimal:2',
        'stock'     => 'integer',
        'is_active' => 'boolean',
    ];

    // Helpers
    public function getDisplayLabelAttribute(): string
    {
        return "{$this->name} ({$this->sku})";
    }
}
