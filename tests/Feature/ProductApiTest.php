<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductApiTest extends TestCase
{

    use RefreshDatabase;

    private function authenticatedUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

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
        $this->authenticatedUser();

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

    public function test_validation_fails_with_invalid_data(): void
    {
        $this->authenticatedUser();

        $response = $this->postJson('/api/products', [
            'name' => '',
            'description' => 123,
            'price' => -10,
            'stock' => -1,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'description', 'price', 'stock']);
    }

    public function test_can_list_products(): void
    {
        $this->authenticatedUser();

        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_can_filter_products_by_name(): void
    {
        $this->authenticatedUser();

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
        $this->authenticatedUser();

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
        $this->authenticatedUser();

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

    public function test_can_update_product(): void
    {
        $this->authenticatedUser();

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

    public function test_can_delete_product(): void
    {
        $this->authenticatedUser();

        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_can_show_product(): void
    {
        $this->authenticatedUser();

        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $product->id);
    }

    public function test_404_when_product_not_found(): void
    {
        $this->authenticatedUser();

        $response = $this->getJson('/api/products/999999');

        $response->assertNotFound();
    }

    public function test_unauthorized_access_denied(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertUnauthorized();
    }
}
