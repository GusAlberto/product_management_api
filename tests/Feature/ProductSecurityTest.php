<?php

namespace Tests\Feature;

class ProductSecurityTest extends ApiTestCase
{
    public function test_unauthorized_access_denied(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertUnauthorized();
    }
}
