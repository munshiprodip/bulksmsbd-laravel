<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Enums;

enum SmsType: string
{
    case TEXT = 'text';
    case UNICODE = 'unicode';

    /**
     * Determine SMS type based on content.
     */
    public static function detect(string $message): self
    {
        return preg_match('/[^\x00-\x7F]/', $message) ? self::UNICODE : self::TEXT;
    }
}
