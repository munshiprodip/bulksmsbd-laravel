<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel;

use BulkSmsBd\Laravel\Enums\SmsType;
use BulkSmsBd\Laravel\Exceptions\BulkSmsBdException;
use BulkSmsBd\Laravel\Exceptions\ResponseCodeMapper;
use Illuminate\Support\Facades\Http;

class BulkSmsBd
{
    protected string $apiKey;
    protected string $senderId;
    protected string $baseUrl;
    protected int $timeout;
    protected bool $throwExceptions;

    /**
     * @param string $apiKey
     * @param string $senderId
     * @param string $baseUrl
     * @param int $timeout
     * @param bool $throwExceptions
     */
    public function __construct(
        string $apiKey = '',
        string $senderId = '',
        string $baseUrl = 'http://bulksmsbd.net',
        int $timeout = 15,
        bool $throwExceptions = true
    ) {
        $this->apiKey = $apiKey;
        $this->senderId = $senderId;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
        $this->throwExceptions = $throwExceptions;
    }

    /**
     * Set API key dynamically.
     */
    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * Set Sender ID dynamically.
     */
    public function setSenderId(string $senderId): self
    {
        $this->senderId = $senderId;
        return $this;
    }

    /**
     * Set Base URL dynamically.
     */
    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    /**
     * Set whether to throw exceptions on non-202 codes.
     */
    public function setThrowExceptions(bool $throwExceptions): self
    {
        $this->throwExceptions = $throwExceptions;
        return $this;
    }

    /**
     * Send one message to single or multiple numbers (One-to-Many).
     *
     * @param string|array<int, string> $numbers
     * @param string $message
     * @param SmsType|string|null $type
     * @param string|null $senderId
     * @return array<string, mixed>
     *
     * @throws BulkSmsBdException
     */
    public function send(
        string|array $numbers,
        string $message,
        SmsType|string|null $type = null,
        ?string $senderId = null
    ): array {
        if (is_array($numbers)) {
            $numbers = implode(',', $numbers);
        }

        $resolvedSenderId = $senderId ?? $this->senderId;

        $payload = [
            'api_key'  => $this->apiKey,
            'senderid' => $resolvedSenderId,
            'number'   => $numbers,
            'message'  => $message,
        ];

        if ($type !== null) {
            $payload['type'] = $type instanceof SmsType ? $type->value : $type;
        }

        $url = "{$this->baseUrl}/api/smsapi";

        $response = Http::asForm()
            ->timeout($this->timeout)
            ->post($url, $payload)
            ->json();

        return $this->formatResponse($response);
    }

    /**
     * Send different messages to different numbers (Many-to-Many).
     *
     * @param array<int, array{to: string, message: string}> $messages
     * @param string|null $senderId
     * @return array<string, mixed>
     *
     * @throws BulkSmsBdException
     */
    public function sendMany(array $messages, ?string $senderId = null): array
    {
        $resolvedSenderId = $senderId ?? $this->senderId;
        $url = "{$this->baseUrl}/api/smsapimany";

        $response = Http::asForm()
            ->timeout($this->timeout)
            ->post($url, [
                'api_key'  => $this->apiKey,
                'senderid' => $resolvedSenderId,
                'messages' => json_encode($messages),
            ])->json();

        return $this->formatResponse($response);
    }

    /**
     * Check account credit balance.
     *
     * @param string $method
     * @return array<string, mixed>
     *
     * @throws BulkSmsBdException
     */
    public function getBalance(string $method = 'GET'): array
    {
        $url = "{$this->baseUrl}/api/getBalanceApi";
        $params = ['api_key' => $this->apiKey];

        $httpResponse = match (strtoupper($method)) {
            'POST' => Http::asForm()->timeout($this->timeout)->post($url, $params),
            default => Http::timeout($this->timeout)->get($url, $params),
        };

        $response = $httpResponse->json();
        if ($response === null && is_numeric(trim($httpResponse->body()))) {
            $response = [
                'balance' => (float) trim($httpResponse->body()),
                'response_code' => 202,
            ];
        }

        return $this->formatResponse($response);
    }

    /**
     * Enrich the API response with descriptive message mapped from response_code.
     *
     * @param array<string, mixed>|null $response
     * @return array<string, mixed>
     *
     * @throws BulkSmsBdException
     */
    protected function formatResponse(?array $response): array
    {
        if (!$response) {
            $result = [
                'success' => false,
                'response_code' => 1005,
                'message' => BulkSmsBdException::getMessageForCode(1005),
                'status_message' => BulkSmsBdException::getMessageForCode(1005),
                'is_success' => false,
            ];

            if ($this->throwExceptions) {
                throw ResponseCodeMapper::mapToException(1005, $result['message'], $result);
            }

            return $result;
        }

        $code = $response['response_code'] ?? null;

        if ($code !== null) {
            $codeInt = (int) $code;
            $response['status_message'] = BulkSmsBdException::getMessageForCode($codeInt);
            $response['is_success'] = ($codeInt === 202);

            if ($codeInt !== 202 && $this->throwExceptions) {
                throw ResponseCodeMapper::mapToException($codeInt, $response['status_message'], $response);
            }
        }

        return $response;
    }
}
