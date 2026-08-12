<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Tests\Unit;

use BulkSmsBd\Laravel\Exceptions\AuthenticationException;
use BulkSmsBd\Laravel\Exceptions\BulkSmsBdException;
use BulkSmsBd\Laravel\Exceptions\InsufficientBalanceException;
use BulkSmsBd\Laravel\Exceptions\InvalidSenderIdException;
use BulkSmsBd\Laravel\Exceptions\ResponseCodeMapper;
use BulkSmsBd\Laravel\Exceptions\ServerException;
use BulkSmsBd\Laravel\Exceptions\ValidationException;
use BulkSmsBd\Laravel\Facades\BulkSmsBd;
use BulkSmsBd\Laravel\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ExceptionMapperTest extends TestCase
{
    public function test_get_message_for_code_returns_correct_descriptions(): void
    {
        $this->assertEquals('SMS Submitted Successfully', BulkSmsBdException::getMessageForCode(202));
        $this->assertEquals('Invalid Number', BulkSmsBdException::getMessageForCode(1001));
        $this->assertEquals('Sender ID not correct or sender ID is disabled', BulkSmsBdException::getMessageForCode(1002));
        $this->assertEquals('Insufficient Balance', BulkSmsBdException::getMessageForCode(1007));
        $this->assertEquals('IP Not whitelisted', BulkSmsBdException::getMessageForCode(1032));
        $this->assertEquals('Unknown API Error (Code: 9999)', BulkSmsBdException::getMessageForCode(9999));
    }

    public function test_maps_codes_to_specific_exceptions(): void
    {
        $expectedExceptions = [
            1001 => AuthenticationException::class,
            1002 => InvalidSenderIdException::class,
            1003 => ValidationException::class,
            1005 => ServerException::class,
            1006 => InsufficientBalanceException::class,
            1007 => InsufficientBalanceException::class,
            1011 => AuthenticationException::class,
            1012 => ValidationException::class,
            1013 => InvalidSenderIdException::class,
            1014 => InvalidSenderIdException::class,
            1015 => InvalidSenderIdException::class,
            1016 => InvalidSenderIdException::class,
            1017 => InvalidSenderIdException::class,
            1018 => AuthenticationException::class,
            1019 => InvalidSenderIdException::class,
            1020 => AuthenticationException::class,
            1021 => InvalidSenderIdException::class,
            1031 => ValidationException::class,
            1032 => ServerException::class,
        ];

        foreach ($expectedExceptions as $code => $expectedClass) {
            $exception = ResponseCodeMapper::mapToException($code);

            $this->assertInstanceOf(
                $expectedClass,
                $exception,
                "Failed asserting code {$code} maps to {$expectedClass}"
            );
            $this->assertEquals($code, $exception->getCode());
        }
    }

    public function test_formats_real_gateway_success_and_error_responses(): void
    {
        // 1. Code 202 Success Response
        Http::fake([
            'http://bulksmsbd.net/api/smsapi' => Http::response([
                'response_code' => 202,
                'message_id' => 7966389,
                'success_message' => 'SMS Submitted Successfully 1',
                'error_message' => '',
            ], 200),
        ]);

        $res1 = BulkSmsBd::setThrowExceptions(false)->send('01700000000', 'Test 1');
        $this->assertTrue($res1['is_success']);
        $this->assertEquals(7966389, $res1['message_id']);
        $this->assertEquals('SMS Submitted Successfully 1', $res1['status_message']);

        // 2. Code 1001 Invalid Number
        Http::fake([
            'http://bulksmsbd.net/api/smsapi' => Http::response([
                'response_code' => 1001,
                'success_message' => '',
                'error_message' => 'Invalid Number!',
            ], 200),
        ]);

        $res2 = BulkSmsBd::setThrowExceptions(false)->send('01700000000', 'Test 2');
        $this->assertFalse($res2['is_success']);
        $this->assertEquals('Invalid Number!', $res2['status_message']);

        // 3. Code 1032 IP Not Whitelisted
        Http::fake([
            'http://bulksmsbd.net/api/smsapi' => Http::response([
                'response_code' => 1032,
                'success_message' => '',
                'error_message' => 'Your ip 115.127.145.116 not Whitelisted. Please whitelist ip from Phonebook',
            ], 200),
        ]);

        $res3 = BulkSmsBd::setThrowExceptions(false)->send('01700000000', 'Test 3');
        $this->assertFalse($res3['is_success']);
        $this->assertEquals('Your ip 115.127.145.116 not Whitelisted. Please whitelist ip from Phonebook', $res3['status_message']);
    }
}
