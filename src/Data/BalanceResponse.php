<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Data;

use BulkSmsBd\Laravel\Exceptions\BulkSmsBdException;

/**
 * Data Transfer Object representing an account credit balance query response.
 */
class BalanceResponse
{
    /**
     * @param float $balance Account remaining credit balance amount
     * @param int|null $responseCode Numeric status code from BulkSMSBD API (default 202)
     * @param string|null $message Status message description
     * @param array<string, mixed> $rawResponse Full raw response array payload from API
     */
    public function __construct(
        public readonly float $balance,
        public readonly ?int $responseCode = 202,
        public readonly ?string $message = 'SMS Submitted Successfully',
        public readonly array $rawResponse = []
    ) {
    }

    /**
     * Parse raw response (array, numeric string, or JSON) into a BalanceResponse DTO.
     *
     * @param array<string, mixed>|string|float|int $raw
     * @return self
     */
    public static function fromRaw(mixed $raw): self
    {
        if (is_array($raw)) {
            $balanceVal = round((float) ($raw['balance'] ?? $raw['amount'] ?? $raw['user_balance'] ?? 0.0), 4);
            $code = isset($raw['response_code']) ? (int) $raw['response_code'] : 202;
            $msg = $raw['status_message'] ?? $raw['message'] ?? $raw['success_message'] ?? BulkSmsBdException::getMessageForCode($code);

            return new self(
                balance: $balanceVal,
                responseCode: $code,
                message: is_string($msg) ? $msg : BulkSmsBdException::getMessageForCode($code),
                rawResponse: $raw
            );
        }

        if (is_numeric($raw)) {
            $val = round((float) $raw, 4);
            return new self(
                balance: $val,
                responseCode: 202,
                message: BulkSmsBdException::getMessageForCode(202),
                rawResponse: ['balance' => $val]
            );
        }

        // If string containing JSON payload or plain numeric balance
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return static::fromRaw($decoded);
            }

            if (is_numeric(trim($raw))) {
                $val = round((float) trim($raw), 4);
                return new self(
                    balance: $val,
                    responseCode: 202,
                    message: BulkSmsBdException::getMessageForCode(202),
                    rawResponse: ['balance' => $val]
                );
            }
        }

        return new self(
            balance: 0.0,
            responseCode: 1005,
            message: BulkSmsBdException::getMessageForCode(1005),
            rawResponse: is_array($raw) ? $raw : ['raw' => $raw]
        );
    }

    /**
     * Convert DTO to array structure.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'balance' => $this->balance,
            'response_code' => $this->responseCode,
            'status_message' => $this->message,
            'is_success' => ($this->responseCode === 202),
            'raw_response' => $this->rawResponse,
        ];
    }
}
