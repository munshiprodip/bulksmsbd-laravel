<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Tests\Feature;

use BulkSmsBd\Laravel\Enums\SmsType;
use BulkSmsBd\Laravel\Facades\BulkSmsBd;
use BulkSmsBd\Laravel\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class OneToManySmsTest extends TestCase
{
    public function test_can_send_sms_to_single_recipient(): void
    {
        Http::fake([
            'http://bulksmsbd.net/api/smsapi' => Http::response([
                'response_code' => 202,
                'success_message' => 'SMS Submitted Successfully',
            ], 200),
        ]);

        $response = BulkSmsBd::send('01700000000', 'Test message content');

        $this->assertTrue($response['is_success']);
        $this->assertEquals(202, $response['response_code']);
        $this->assertEquals('SMS Submitted Successfully', $response['status_message']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/api/smsapi')
                && $request['api_key'] === 'test_api_key_123'
                && $request['senderid'] === '8801700000000'
                && $request['number'] === '01700000000'
                && $request['message'] === 'Test message content';
        });
    }

    public function test_can_send_sms_to_comma_separated_recipients(): void
    {
        Http::fake([
            'http://bulksmsbd.net/api/smsapi' => Http::response([
                'response_code' => 202,
                'success_message' => 'SMS Submitted Successfully',
            ], 200),
        ]);

        $response = BulkSmsBd::send('01700000000,01800000000', 'Bulk text message');

        $this->assertTrue($response['is_success']);

        Http::assertSent(function ($request) {
            return $request['number'] === '01700000000,01800000000';
        });
    }

    public function test_can_send_sms_to_array_of_recipients(): void
    {
        Http::fake([
            'http://bulksmsbd.net/api/smsapi' => Http::response([
                'response_code' => 202,
                'success_message' => 'SMS Submitted Successfully',
            ], 200),
        ]);

        $response = BulkSmsBd::send(['01700000000', '01800000000', '01900000000'], 'Array payload message');

        $this->assertTrue($response['is_success']);

        Http::assertSent(function ($request) {
            return $request['number'] === '01700000000,01800000000,01900000000';
        });
    }

    public function test_allows_explicit_sms_type_enum_and_sender_id_override(): void
    {
        Http::fake([
            'http://bulksmsbd.net/api/smsapi' => Http::response([
                'response_code' => 202,
                'success_message' => 'SMS Submitted Successfully',
            ], 200),
        ]);

        $response = BulkSmsBd::send(
            numbers: '01700000000',
            message: 'Custom sender test',
            type: SmsType::UNICODE,
            senderId: 'CUSTOM_SENDER'
        );

        $this->assertTrue($response['is_success']);

        Http::assertSent(function ($request) {
            return $request['senderid'] === 'CUSTOM_SENDER'
                && $request['type'] === 'unicode';
        });
    }
}
