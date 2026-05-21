<?php

namespace Tests\Feature;

use App\Models\Product;

class ProductShowTest extends ApiTestCase
{
    public function test_can_show_product(): void
    {
        $this->authenticate();

        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $product->id);
    }

    public function test_404_when_product_not_found(): void
    {
        $this->authenticate();

        $response = $this->getJson('/api/products/999999');

        $response->assertNotFound();
    }
}
