<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SellTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');

        $purchaseData = [
            'quantity' => 5,
            'transaction_date' => '2019-05-01'
        ];
        $this->postJson('/api/purchase/banana', $purchaseData);
    }

    public function testBasicSell()
    {
        $sellData = [
            'quantity' => 1,
            'transaction_date' => '2019-05-01'
        ];
        $response = $this->postJson('/api/sell/banana', $sellData);

        $response->assertStatus(201);

        $response->assertJson(['success' => true]);

        $sellData['price_per_item'] = 0.35;
        $sellData['transaction_type'] = 'sell';
        $this->assertDatabaseHas('transactions', $sellData);
    }

    public function testSellWithPrice()
    {
        $data = [
            'quantity' => 1,
            'transaction_date' => '2019-05-01',
            'price' => 0.90,
        ];
        $response = $this->postJson('/api/sell/banana', $data);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);
        $price = $data['price'];
        unset($data['price']);
        $data['price_per_item'] = $price;
        $data['transaction_type'] = 'sell';
        $this->assertDatabaseHas('transactions', $data);
    }

    public function testSellWithNoInventory()
    {
        $data = [
            'quantity' => 15,
            'transaction_date' => '2019-05-01',
        ];
        $response = $this->postJson('/api/sell/banana', $data);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    public function testSellAllInventory()
    {
        $sellData = [
            'quantity' => 5,
            'transaction_date' => '2019-05-01'
        ];
        $response = $this->postJson('/api/sell/banana', $sellData);

        $response->assertStatus(201);

        $response->assertJson(['success' => true]);

        $sellData['price_per_item'] = 0.35;
        $sellData['transaction_type'] = 'sell';
        $this->assertDatabaseHas('transactions', $sellData);
    }

    public function testSellAfterExpiration()
    {
        $sellData = [
            'quantity' => 5,
            'transaction_date' => '2019-05-12'
        ];
        $response = $this->postJson('/api/sell/banana', $sellData);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'errors' => [
                'quantity' => [
                    "Requested quantity is more than inventory"
                ]
            ]
        ]);
    }

}
