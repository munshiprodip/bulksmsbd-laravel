<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array send(string|array $numbers, string $message, \BulkSmsBd\Laravel\Enums\SmsType|string|null $type = null, ?string $senderId = null)
 * @method static array sendMany(array $messages, ?string $senderId = null)
 * @method static array getBalance(string $method = 'GET')
 * @method static \BulkSmsBd\Laravel\BulkSmsBd setApiKey(string $apiKey)
 * @method static \BulkSmsBd\Laravel\BulkSmsBd setSenderId(string $senderId)
 * @method static \BulkSmsBd\Laravel\BulkSmsBd setBaseUrl(string $baseUrl)
 * @method static \BulkSmsBd\Laravel\BulkSmsBd setThrowExceptions(bool $throw)
 *
 * @see \BulkSmsBd\Laravel\BulkSmsBd
 */
class BulkSmsBd extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'bulksmsbd';
    }
}
