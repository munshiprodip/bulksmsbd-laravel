<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Exceptions;

use Exception;

class BulkSmsBdException extends Exception
{
    /**
     * Map BulkSMSBD status codes to human-readable error messages.
     *
     * @var array<int, string>
     */
    public static array $statusCodes = [
        202  => 'SMS Submitted Successfully',
        1001 => 'Invalid Number',
        1002 => 'Sender ID not correct or sender ID is disabled',
        1003 => 'Required fields missing / Contact Your System Administrator',
        1005 => 'Internal Error',
        1006 => 'Balance Validity Not Available',
        1007 => 'Insufficient Balance',
        1011 => 'User ID not found',
        1012 => 'Masking SMS must be sent in Bengali',
        1013 => 'Sender ID has not found Gateway by API key',
        1014 => 'Sender Type Name not found using this sender by API key',
        1015 => 'Sender ID has not found Any Valid Gateway by API key',
        1016 => 'Sender Type Name Active Price Info not found by this sender ID',
        1017 => 'Sender Type Name Price Info not found by this sender ID',
        1018 => 'The Owner of this Account is disabled',
        1019 => 'The Price of this Account is disabled for this sender type',
        1020 => 'The parent of this account is not found',
        1021 => 'The parent active price of this account is not found',
        1031 => 'Your Account Not Verified, Please Contact Administrator',
        1032 => 'IP Not whitelisted',
    ];

    /**
     * @param string $message
     * @param int $code
     * @param array<string, mixed> $rawResponse
     * @param Exception|null $previous
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        protected array $rawResponse = [],
        ?Exception $previous = null
    ) {
        if (empty($message) && $code > 0) {
            $message = static::getMessageForCode($code);
        }
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get descriptive message for numeric status code.
     */
    public static function getMessageForCode(int|string $code): string
    {
        $code = (int) $code;
        return static::$statusCodes[$code] ?? "Unknown API Error (Code: {$code})";
    }

    /**
     * Get the raw API response data payload.
     *
     * @return array<string, mixed>
     */
    public function getRawResponse(): array
    {
        return $this->rawResponse;
    }
}
