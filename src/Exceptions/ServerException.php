<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Exceptions;

/**
 * Exception thrown when gateway internal database, rate limiting, IP restriction, or server timeouts occur (e.g. codes 1005, 1023, 1025-1028, 1030, 1032).
 */
class ServerException extends BulkSmsBdException
{
}
