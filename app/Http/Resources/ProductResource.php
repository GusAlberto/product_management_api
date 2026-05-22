<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read \App\Models\Product $resource
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * Product identifier.
             * @example 1
             */
            'id' => $this->id,
            /**
             * Product name.
             * @example "Wireless Mouse"
             */
            'name' => $this->name,
            /**
             * Product description.
             * @example "Compact wireless mouse with silent clicks."
             */
            'description' => $this->description,
            /**
             * Product price.
             * @example 129.9
             */
            'price' => (float) $this->price,
            /**
             * Available stock.
             * @example 50
             */
            'stock' => $this->stock,
            /**
             * Creation timestamp.
             * @example "2026-05-21T18:10:00.000000Z"
             */
            'created_at' => $this->created_at?->toISOString(),
            /**
             * Last update timestamp.
             * @example "2026-05-21T18:10:00.000000Z"
             */
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
