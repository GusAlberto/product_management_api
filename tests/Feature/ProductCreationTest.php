<?php

namespace Tests\Feature;

class ProductCreationTest extends ApiTestCase
{
    public function test_can_create_product(): void
    {
        $this->authenticate();

        $response = $this->postJson('/api/products', [
            'name' => 'Mouse Gamer',
            'description' => 'Mouse RGB',
            'price' => 199.90,
            'stock' => 10,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Mouse Gamer');

        $this->assertDatabaseHas('products', [
            'name' => 'Mouse Gamer',
            'stock' => 10,
        ]);
    }
}
