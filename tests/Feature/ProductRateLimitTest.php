<?php

namespace Tests\Feature;

class ProductRateLimitTest extends ApiTestCase
{
    public function test_product_api_is_rate_limited(): void
    {
        $this->authenticate();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->getJson('/api/products')->assertOk();
        }

        $this->getJson('/api/products')->assertTooManyRequests();
    }
}