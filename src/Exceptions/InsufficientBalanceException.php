<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Exceptions;

/**
 * Exception thrown when the BulkSMSBD account has insufficient balance or expired balance validity (e.g. codes 1006, 1007).
 */
class InsufficientBalanceException extends BulkSmsBdException
{
}
