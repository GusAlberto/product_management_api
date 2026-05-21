<?php

namespace Tests\Feature;

use App\Models\Product;

class ProductUpdateTest extends ApiTestCase
{
    public function test_can_update_product(): void
    {
        $this->authenticate();

        $product = Product::factory()->create([
            'name' => 'Mouse Antigo',
            'description' => 'Descricao antiga',
            'price' => 100,
            'stock' => 5,
        ]);

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Mouse Novo',
            'description' => 'Descricao atualizada',
            'price' => 150,
            'stock' => 8,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Mouse Novo')
            ->assertJsonPath('data.price', 150)
            ->assertJsonPath('data.stock', 8);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Mouse Novo',
            'stock' => 8,
        ]);
    }
}
