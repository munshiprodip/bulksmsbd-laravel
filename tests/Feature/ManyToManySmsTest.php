<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Tests\Feature;

use BulkSmsBd\Laravel\Facades\BulkSmsBd;
use BulkSmsBd\Laravel\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ManyToManySmsTest extends TestCase
{
    public function test_can_send_many_to_many_sms_messages(): void
    {
        Http::fake([
            'http://bulksmsbd.net/api/smsapimany' => Http::response([
                'response_code' => 202,
                'success_message' => 'SMS Submitted Successfully',
            ], 200),
        ]);

        $payload = [
            ['to' => '01711111111', 'message' => 'Hello User 1, your OTP is 1234'],
            ['to' => '01822222222', 'message' => 'Hello User 2, your OTP is 5678'],
        ];

        $response = BulkSmsBd::sendMany($payload);

        $this->assertTrue($response['is_success']);
        $this->assertEquals(202, $response['response_code']);
        $this->assertEquals('SMS Submitted Successfully', $response['status_message']);

        Http::assertSent(function ($request) use ($payload) {
            $decodedMessages = json_decode($request['messages'], true);

            return str_contains($request->url(), '/api/smsapimany')
                && $request['api_key'] === 'test_api_key_123'
                && $request['senderid'] === '8801700000000'
                && count($decodedMessages) === 2
                && $decodedMessages[0]['to'] === '01711111111'
                && $decodedMessages[1]['message'] === 'Hello User 2, your OTP is 5678';
        });
    }

    public function test_many_to_many_sms_supports_sender_id_override(): void
    {
        Http::fake([
            'http://bulksmsbd.net/api/smsapimany' => Http::response([
                'response_code' => 202,
                'success_message' => 'SMS Submitted Successfully',
            ], 200),
        ]);

        $payload = [
            ['to' => '01711111111', 'message' => 'Notification 1'],
        ];

        $response = BulkSmsBd::sendMany($payload, 'BRAND_ID');

        $this->assertTrue($response['is_success']);

        Http::assertSent(function ($request) {
            return $request['senderid'] === 'BRAND_ID';
        });
    }
}
