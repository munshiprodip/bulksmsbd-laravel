<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Tests\Feature;

use BulkSmsBd\Laravel\Facades\BulkSmsBd;
use BulkSmsBd\Laravel\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class BalanceTest extends TestCase
{
    public function test_can_check_balance_via_get_method(): void
    {
        Http::fake([
            'http://bulksmsbd.net/api/getBalanceApi*' => Http::response([
                'balance' => '250.75',
                'response_code' => 202,
            ], 200),
        ]);

        $response = BulkSmsBd::getBalance('GET');

        $this->assertEquals(250.75, $response['balance']);
        $this->assertEquals(202, $response['response_code']);
        $this->assertTrue($response['is_success']);
        $this->assertEquals('SMS Submitted Successfully', $response['status_message']);

        Http::assertSent(function ($request) {
            return $request->method() === 'GET'
                && str_contains($request->url(), '/api/getBalanceApi')
                && $request['api_key'] === 'test_api_key_123';
        });
    }

    public function test_can_check_balance_via_post_method(): void
    {
        Http::fake([
            'http://bulksmsbd.net/api/getBalanceApi' => Http::response('500.00', 200),
        ]);

        $response = BulkSmsBd::getBalance('POST');

        $this->assertEquals(500.00, $response['balance']);
        $this->assertEquals(202, $response['response_code']);
        $this->assertTrue($response['is_success']);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && str_contains($request->url(), '/api/getBalanceApi')
                && $request['api_key'] === 'test_api_key_123';
        });
    }

    public function test_cleans_high_precision_floating_point_balance_output(): void
    {
        Http::fake([
            'http://bulksmsbd.net/api/getBalanceApi*' => Http::response([
                'response_code' => 202,
                'balance' => 49.64999999999999857891452847979962825775146484375,
            ], 200),
        ]);

        $response = BulkSmsBd::getBalance('GET');

        $this->assertEquals(49.65, $response['balance']);
        $this->assertTrue($response['is_success']);
    }
}
