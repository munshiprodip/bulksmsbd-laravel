<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Exceptions;

/**
 * Exception thrown when API key authentication or user account validation fails (e.g. codes 1001, 1011, 1018, 1020, 1022).
 */
class AuthenticationException extends BulkSmsBdException
{
}
