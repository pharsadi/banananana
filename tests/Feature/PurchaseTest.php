<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testBasicPurchase()
    {
        $data = [
            'quantity' => 5,
            'transaction_date' => '2019-05-01'
        ];
        $response = $this->postJson('/api/purchase/banana', $data);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);
        $data['price_per_item'] = 0.20;
        $data['transaction_type'] = 'purchase';
        $this->assertDatabaseHas('transactions', $data);
    }

    public function testPurchaseWithPrice()
    {
        $data = [
            'quantity' => 5,
            'transaction_date' => '2019-05-01',
            'price' => 0.10,
        ];
        $response = $this->postJson('/api/purchase/banana', $data);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);
        $price = $data['price'];
        unset($data['price']);
        $data['price_per_item'] = $price;
        $data['transaction_type'] = 'purchase';
        $this->assertDatabaseHas('transactions', $data);
    }

    public function testPurchaseWithInvalidDateFormat()
    {
        $data = [
            'quantity' => 5,
            'transaction_date' => '20190501',
        ];
        $response = $this->postJson('/api/purchase/banana', $data);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    public function testPurchaseWithZeroQuantity()
    {
        $data = [
            'quantity' => 0,
            'transaction_date' => '2019-05-01',
        ];
        $response = $this->postJson('/api/purchase/banana', $data);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    public function testPurchaseWithNegativeQuantity()
    {
        $data = [
            'quantity' => 0,
            'transaction_date' => '2019-05-01',
        ];
        $response = $this->postJson('/api/purchase/banana', $data);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }
}
