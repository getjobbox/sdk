<?php

declare(strict_types=1);

namespace GetJobBox\Exceptions;

use Exception;

/**
 * HTTP API error response from JobBox.
 *
 * Note: API `code` is exposed as {@see $apiCode} because {@see Exception::$code} is reserved.
 */
final class JobBoxApiException extends Exception
{
    public readonly int $status;

    public readonly ?string $apiCode;

    public readonly ?string $requestId;

    public readonly mixed $body;

    public function __construct(
        string $message,
        int $status,
        ?string $apiCode = null,
        ?string $requestId = null,
        mixed $body = null,
    ) {
        parent::__construct($message);
        $this->status = $status;
        $this->apiCode = $apiCode;
        $this->requestId = $requestId;
        $this->body = $body;
    }
}
