<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductApiTest extends TestCase
{

    use RefreshDatabase;

    // /**
    //  * A basic feature test example.
    //  */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    public function test_can_create_product(): void
    {
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

    public function test_can_list_products(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_can_paginate_products_and_keep_cache_separated_by_page(): void
    {
        $products = Product::factory()->count(12)->create();

        $firstPage = $this->getJson('/api/products?per_page=5&page=1');
        $secondPage = $this->getJson('/api/products?per_page=5&page=2');

        $firstPage
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.meta.current_page', 1)
            ->assertJsonPath('data.meta.per_page', 5)
            ->assertJsonPath('data.meta.total', 12)
            ->assertJsonCount(5, 'data.items');

        $secondPage
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.meta.current_page', 2)
            ->assertJsonPath('data.meta.per_page', 5)
            ->assertJsonCount(5, 'data.items');

        $firstPageIds = collect($firstPage->json('data.items'))->pluck('id');
        $secondPageIds = collect($secondPage->json('data.items'))->pluck('id');

        $this->assertNotEquals($firstPageIds->all(), $secondPageIds->all());
        $this->assertTrue($products->pluck('id')->contains($firstPageIds->first()));
    }

    public function test_can_show_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $product->id);
    }
}
