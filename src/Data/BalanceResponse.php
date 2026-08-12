<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Data;

class BalanceResponse
{
    /**
     * @param float $balance
     * @param int|null $responseCode
     * @param string|null $message
     * @param array<string, mixed> $rawResponse
     */
    public function __construct(
        public readonly float $balance,
        public readonly ?int $responseCode = 202,
        public readonly ?string $message = 'Balance retrieved successfully',
        public readonly array $rawResponse = []
    ) {
    }

    /**
     * Parse raw response into BalanceResponse DTO.
     *
     * @param array<string, mixed>|string|float|int $raw
     * @return self
     */
    public static function fromRaw(mixed $raw): self
    {
        if (is_array($raw)) {
            $balanceVal = (float) ($raw['balance'] ?? $raw['amount'] ?? $raw['user_balance'] ?? 0.0);
            $code = isset($raw['response_code']) ? (int) $raw['response_code'] : 202;
            $msg = $raw['message'] ?? $raw['success_message'] ?? 'Balance retrieved successfully';

            return new self(
                balance: $balanceVal,
                responseCode: $code,
                message: is_string($msg) ? $msg : null,
                rawResponse: $raw
            );
        }

        if (is_numeric($raw)) {
            $val = (float) $raw;
            return new self(
                balance: $val,
                responseCode: 202,
                message: 'Balance retrieved successfully',
                rawResponse: ['balance' => $val]
            );
        }

        // If string containing JSON or plain number
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return static::fromRaw($decoded);
            }

            if (is_numeric(trim($raw))) {
                $val = (float) trim($raw);
                return new self(
                    balance: $val,
                    responseCode: 202,
                    message: 'Balance retrieved successfully',
                    rawResponse: ['balance' => $val]
                );
            }
        }

        return new self(
            balance: 0.0,
            responseCode: 1005,
            message: 'Unable to parse balance response',
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
            'message' => $this->message,
            'raw_response' => $this->rawResponse,
        ];
    }
}
