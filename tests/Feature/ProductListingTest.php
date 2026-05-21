<?php

namespace Tests\Feature;

use App\Models\Product;

class ProductListingTest extends ApiTestCase
{
    public function test_can_list_products(): void
    {
        $this->authenticate();

        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_can_filter_products_by_name(): void
    {
        $this->authenticate();

        Product::factory()->create(['name' => 'Mouse Gamer']);
        Product::factory()->create(['name' => 'Teclado Mecânico']);

        $response = $this->getJson('/api/products?name=mouse');

        $response
            ->assertOk()
            ->assertJsonPath('success', true);

        $items = collect($response->json('data.items'));

        $this->assertCount(1, $items);
        $this->assertSame('Mouse Gamer', $items->first()['name']);
    }

    public function test_can_filter_products_by_price_range(): void
    {
        $this->authenticate();

        Product::factory()->create(['name' => 'Produto Barato', 'price' => 49.90]);
        Product::factory()->create(['name' => 'Produto Médio', 'price' => 120]);
        Product::factory()->create(['name' => 'Produto Caro', 'price' => 250]);

        $response = $this->getJson('/api/products?min_price=50&max_price=200');

        $response
            ->assertOk()
            ->assertJsonPath('success', true);

        $items = collect($response->json('data.items'));

        $this->assertCount(1, $items);
        $this->assertSame('Produto Médio', $items->first()['name']);
    }

    public function test_can_paginate_products_and_keep_cache_separated_by_page(): void
    {
        $this->authenticate();

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
}
