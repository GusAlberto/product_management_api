<?php

namespace Tests\Feature;

use App\Models\Product;

class ProductDeletionTest extends ApiTestCase
{
    public function test_can_delete_product(): void
    {
        $this->authenticate();

        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
