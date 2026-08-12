<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Exceptions;

/**
 * Exception thrown when SMS input parameters (number, message text, type, recipient limit) fail validation (e.g. codes 1003, 1004, 1008-1010, 1012, 1024, 1029, 1031).
 */
class ValidationException extends BulkSmsBdException
{
}
