<?php

declare(strict_types=1);

namespace GetJobBox\Exceptions;

use Exception;
use Throwable;

/**
 * Transport / timeout failure talking to JobBox.
 */
final class JobBoxNetworkException extends Exception
{
    public function __construct(
        string $message,
        public readonly mixed $causeError = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
