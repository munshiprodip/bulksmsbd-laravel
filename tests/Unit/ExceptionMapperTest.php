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

    public function test_client_returns_formatted_response_array_when_throw_exceptions_disabled(): void
    {
        Http::fake([
            'http://bulksmsbd.net/api/smsapi' => Http::response([
                'response_code' => 1007,
                'error_message' => 'Insufficient Balance',
            ], 200),
        ]);

        $response = BulkSmsBd::setThrowExceptions(false)->send('01700000000', 'Test message');

        $this->assertFalse($response['is_success']);
        $this->assertEquals(1007, $response['response_code']);
        $this->assertEquals('Insufficient Balance', $response['status_message']);
    }
}
