<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Exceptions;

/**
 * Exception thrown when the specified Sender ID is invalid, unassigned, or disabled (e.g. codes 1002, 1013-1017, 1019, 1021).
 */
class InvalidSenderIdException extends BulkSmsBdException
{
}
