<?php

declare(strict_types=1);

namespace GetJobBox\Http;

/**
 * Low-level HTTP transport.
 *
 * @phpstan-type TransportResponse array{0: int, 1: array<string, string>, 2: string}
 */
interface TransportInterface
{
    /**
     * @param array<string, string> $headers
     * @return array{0: int, 1: array<string, string>, 2: string} status, headers, body
     */
    public function request(
        string $method,
        string $url,
        array $headers,
        ?string $body,
        float $timeoutSeconds,
    ): array;
}
