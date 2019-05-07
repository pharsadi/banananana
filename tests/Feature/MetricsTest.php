<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MetricsTest extends TestCase
{
    use RefreshDatabase;

    /** @var  \App\Repositories\Transaction */
    protected $repo;

    /** @var \App\Item */
    protected $item;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');

        $model = new \App\Transaction();
        $this->repo = new \App\Repositories\Transaction($model);

        $this->item = \App\Item::where('name', 'banana')->first();

        for ($i = 1; $i <= 9; $i++) {
            $purchaseData = [
                'quantity' => 10,
                'transaction_date' => '2019-05-0' . $i
            ];
            $this->repo->purchase($purchaseData, $this->item);
        }
    }

    public function testBasicMetrics()
    {
        $sellData = [
            'quantity' => 5,
            'transaction_date' => '2019-05-01'
        ];
        $this->repo->sell($sellData, $this->item);

        $sellData = [
            'quantity' => 20,
            'transaction_date' => '2019-05-08'
        ];
        $this->repo->sell($sellData, $this->item);

        $query = [
            'start_date' => '2019-05-01',
            'end_date' => '2019-05-09',
        ];
        $response = $this->getJson('/api/metrics/banana?' . http_build_query($query));

        $response->assertOk();
        $response->assertJson([
            'newInventory' => 65,
            'expiredInventory' => 0,
            'soldCount' => 25,
            'profit' => -9.25
        ]);
    }

    public function testMissingDates()
    {
        $response = $this->getJson('/api/metrics/banana');
        $response->assertStatus(400);
    }

    public function testInvalidDateFormat()
    {
        $query = [
            'start_date' => '20190501',
            'end_date' => '2019-05-02',
        ];
        $response = $this->getJson('/api/metrics/banana?' . http_build_query($query));
        $response->assertStatus(400);
    }

    public function testExpirationCountWithoutSales()
    {
        $query = [
            'start_date' => '2019-05-16',
            'end_date' => '2019-05-19',
        ];
        $response = $this->getJson('/api/metrics/banana?' . http_build_query($query));
        $response->assertOk();
        $response->assertJson([
            'newInventory' => 0,
            'expiredInventory' => 40,
            'soldCount' => 0,
        ]);
    }

    public function testExpirationCountWithSales()
    {
        $sellData = [
            'quantity' => 10,
            'transaction_date' => '2019-05-18'
        ];
        $this->repo->sell($sellData, $this->item);

        $query = [
            'start_date' => '2019-05-16',
            'end_date' => '2019-05-19',
        ];
        $response = $this->getJson('/api/metrics/banana?' . http_build_query($query));
        $response->assertOk();
        $response->assertJson([
            'newInventory' => 0,
            'expiredInventory' => 30,
            'soldCount' => 10,
        ]);
    }
}
