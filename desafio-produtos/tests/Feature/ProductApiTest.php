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
}
