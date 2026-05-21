<?php

namespace Tests\Feature;

class ProductValidationTest extends ApiTestCase
{
    public function test_validation_fails_with_invalid_data(): void
    {
        $this->authenticate();

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
}
