<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(
                $filters['name'] ?? null,
                fn(Builder $q, $name) =>
                $q->where('name', 'like', '%' . $name . '%')
            )
            ->when(
                $filters['min_price'] ?? null,
                fn(Builder $q, $min) =>
                $q->where('price', '>=', $min)
            )
            ->when(
                $filters['max_price'] ?? null,
                fn(Builder $q, $max) =>
                $q->where('price', '<=', $max)
            )
            ->when(
                $filters['min_stock'] ?? null,
                fn(Builder $q, $minStock) =>
                $q->where('stock', '>=', $minStock)
            )
            ->when(
                $filters['max_stock'] ?? null,
                fn(Builder $q, $maxStock) =>
                $q->where('stock', '<=', $maxStock)
            );
    }
}
