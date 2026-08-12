<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Data;

use BulkSmsBd\Laravel\Exceptions\BulkSmsBdException;
use BulkSmsBd\Laravel\Exceptions\ResponseCodeMapper;

/**
 * Data Transfer Object representing an SMS dispatch response from BulkSMSBD.
 */
class SmsResponse
{
    /**
     * @param int $responseCode Numeric status code from BulkSMSBD (e.g., 202)
     * @param string $message Descriptive status or error message
     * @param array<string, mixed> $rawResponse Full raw response array payload from API
     * @param string|null $successMessage Gateway success message if present
     */
    public function __construct(
        public readonly int $responseCode,
        public readonly string $message,
        public readonly array $rawResponse = [],
        public readonly ?string $successMessage = null
    ) {
    }

    /**
     * Check whether the request was successful (response code 202).
     */
    public function isSuccessful(): bool
    {
        return ResponseCodeMapper::isSuccess($this->responseCode);
    }

    /**
     * Parse raw API array into an SmsResponse instance.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $code = (int) ($data['response_code'] ?? $data['code'] ?? 0);
        $customMessage = $data['error_message'] ?? $data['success_message'] ?? $data['message'] ?? null;
        $mappedMessage = is_string($customMessage) && !empty($customMessage)
            ? $customMessage
            : BulkSmsBdException::getMessageForCode($code);

        return new self(
            responseCode: $code,
            message: $mappedMessage,
            rawResponse: $data,
            successMessage: is_string($data['success_message'] ?? null) ? $data['success_message'] : null
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
            'is_success' => $this->isSuccessful(),
            'response_code' => $this->responseCode,
            'status_message' => $this->message,
            'success_message' => $this->successMessage,
            'raw_response' => $this->rawResponse,
        ];
    }
}
