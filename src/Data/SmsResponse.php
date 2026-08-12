<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Enums; // oops namespace check

namespace BulkSmsBd\Laravel\Data;

use BulkSmsBd\Laravel\Exceptions\ResponseCodeMapper;

class SmsResponse
{
    /**
     * @param int $responseCode
     * @param string $message
     * @param array<string, mixed> $rawResponse
     * @param string|null $successMessage
     */
    public function __construct(
        public readonly int $responseCode,
        public readonly string $message,
        public readonly array $rawResponse = [],
        public readonly ?string $successMessage = null
    ) {
    }

    /**
     * Check whether the request was successful (code 202).
     */
    public function isSuccessful(): bool
    {
        return ResponseCodeMapper::isSuccess($this->responseCode);
    }

    /**
     * Parse raw API array into SmsResponse object.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $code = (int) ($data['response_code'] ?? $data['code'] ?? 0);
        $customMessage = $data['success_message'] ?? $data['error_message'] ?? $data['message'] ?? null;
        $mappedMessage = ResponseCodeMapper::getMessage($code, is_string($customMessage) ? $customMessage : null);

        return new self(
            responseCode: $code,
            message: $mappedMessage,
            rawResponse: $data,
            successMessage: is_string($customMessage) ? $customMessage : null
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
            'success' => $this->isSuccessful(),
            'response_code' => $this->responseCode,
            'message' => $this->message,
            'success_message' => $this->successMessage,
            'raw_response' => $this->rawResponse,
        ];
    }
}
