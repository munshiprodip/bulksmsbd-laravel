<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Enums;

/**
 * Enum representing supported BulkSMSBD message types.
 */
enum SmsType: string
{
    case TEXT = 'text';
    case UNICODE = 'unicode';

    /**
     * Determine whether an SMS message requires UNICODE encoding (e.g. Bengali script or non-ASCII characters).
     *
     * @param string $message The message text to analyze
     * @return self
     */
    public static function detect(string $message): self
    {
        return preg_match('/[^\x00-\x7F]/', $message) ? self::UNICODE : self::TEXT;
    }
}
