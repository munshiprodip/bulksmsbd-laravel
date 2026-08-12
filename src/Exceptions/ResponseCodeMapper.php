<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Exceptions;

/**
 * Mapper utility to resolve BulkSMSBD numeric response codes (202, 1001-1032) to domain exceptions.
 */
class ResponseCodeMapper
{
    /**
     * Map of BulkSMSBD response codes to human-readable error messages and exception classes.
     *
     * @var array<int, array{message: string, exception: class-string<BulkSmsBdException>}>
     */
    protected static array $mappings = [
        202  => [
            'message' => 'SMS Submitted Successfully',
            'exception' => BulkSmsBdException::class,
        ],
        1001 => [
            'message' => 'Invalid Number',
            'exception' => AuthenticationException::class,
        ],
        1002 => [
            'message' => 'Sender ID not correct or sender ID is disabled',
            'exception' => InvalidSenderIdException::class,
        ],
        1003 => [
            'message' => 'Required fields missing / Contact Your System Administrator',
            'exception' => ValidationException::class,
        ],
        1004 => [
            'message' => 'Invalid Mobile Number Format',
            'exception' => ValidationException::class,
        ],
        1005 => [
            'message' => 'Internal Error',
            'exception' => ServerException::class,
        ],
        1006 => [
            'message' => 'Balance Validity Not Available',
            'exception' => InsufficientBalanceException::class,
        ],
        1007 => [
            'message' => 'Insufficient Balance',
            'exception' => InsufficientBalanceException::class,
        ],
        1008 => [
            'message' => 'SMS Text Message is Empty',
            'exception' => ValidationException::class,
        ],
        1009 => [
            'message' => 'Inactive Account or Invalid SMS Request Type',
            'exception' => ValidationException::class,
        ],
        1010 => [
            'message' => 'Invalid Request Type parameter',
            'exception' => ValidationException::class,
        ],
        1011 => [
            'message' => 'User ID not found',
            'exception' => AuthenticationException::class,
        ],
        1012 => [
            'message' => 'Masking SMS must be sent in Bengali',
            'exception' => ValidationException::class,
        ],
        1013 => [
            'message' => 'Sender ID has not found Gateway by API key',
            'exception' => InvalidSenderIdException::class,
        ],
        1014 => [
            'message' => 'Sender Type Name not found using this sender by API key',
            'exception' => InvalidSenderIdException::class,
        ],
        1015 => [
            'message' => 'Sender ID has not found Any Valid Gateway by API key',
            'exception' => InvalidSenderIdException::class,
        ],
        1016 => [
            'message' => 'Sender Type Name Active Price Info not found by this sender ID',
            'exception' => InvalidSenderIdException::class,
        ],
        1017 => [
            'message' => 'Sender Type Name Price Info not found by this sender ID',
            'exception' => InvalidSenderIdException::class,
        ],
        1018 => [
            'message' => 'The Owner of this Account is disabled',
            'exception' => AuthenticationException::class,
        ],
        1019 => [
            'message' => 'The Price of this Account is disabled for this sender type',
            'exception' => InvalidSenderIdException::class,
        ],
        1020 => [
            'message' => 'The parent of this account is not found',
            'exception' => AuthenticationException::class,
        ],
        1021 => [
            'message' => 'The parent active price of this account is not found',
            'exception' => InvalidSenderIdException::class,
        ],
        1022 => [
            'message' => 'Account is suspended or disabled',
            'exception' => AuthenticationException::class,
        ],
        1023 => [
            'message' => 'API Rate Limit Exceeded',
            'exception' => ServerException::class,
        ],
        1024 => [
            'message' => 'Invalid Schedule Time parameter',
            'exception' => ValidationException::class,
        ],
        1025 => [
            'message' => 'IP address restriction violated',
            'exception' => ServerException::class,
        ],
        1026 => [
            'message' => 'Route not assigned to sender ID',
            'exception' => ServerException::class,
        ],
        1027 => [
            'message' => 'Destination prefix or country code not allowed',
            'exception' => ServerException::class,
        ],
        1028 => [
            'message' => 'BulkSMSBD gateway is currently under maintenance',
            'exception' => ServerException::class,
        ],
        1029 => [
            'message' => 'SMS Content contains banned words or failed spam check',
            'exception' => ValidationException::class,
        ],
        1030 => [
            'message' => 'Duplicate transaction ID or message payload detected',
            'exception' => ServerException::class,
        ],
        1031 => [
            'message' => 'Your Account Not Verified, Please Contact Administrator',
            'exception' => ValidationException::class,
        ],
        1032 => [
            'message' => 'IP Not whitelisted',
            'exception' => ServerException::class,
        ],
    ];

    /**
     * Check if a given code represents success (202).
     */
    public static function isSuccess(int $code): bool
    {
        return $code === 202;
    }

    /**
     * Get the mapped description for a given status code.
     */
    public static function getMessage(int $code, ?string $fallback = null): string
    {
        if ($fallback !== null && !empty($fallback)) {
            return $fallback;
        }

        return BulkSmsBdException::getMessageForCode($code);
    }

    /**
     * Get all defined code mappings.
     *
     * @return array<int, array{message: string, exception: class-string<BulkSmsBdException>}>
     */
    public static function getMappings(): array
    {
        return static::$mappings;
    }

    /**
     * Map a response code and payload to a concrete PHP Exception instance.
     *
     * @param int $code
     * @param string|null $customMessage
     * @param array<string, mixed> $rawResponse
     * @return BulkSmsBdException
     */
    public static function mapToException(
        int $code,
        ?string $customMessage = null,
        array $rawResponse = []
    ): BulkSmsBdException {
        $mapping = static::$mappings[$code] ?? null;
        $message = $customMessage ?? BulkSmsBdException::getMessageForCode($code);
        $exceptionClass = $mapping['exception'] ?? BulkSmsBdException::class;

        return new $exceptionClass($message, $code, $rawResponse);
    }
}
