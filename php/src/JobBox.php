<?php

declare(strict_types=1);

namespace GetJobBox;

use GetJobBox\Http\Client;
use GetJobBox\Http\CurlTransport;
use GetJobBox\Http\TransportInterface;
use GetJobBox\Resources\Jobs;
use InvalidArgumentException;

/**
 * Official JobBox PHP SDK client (Jobs resource).
 */
final class JobBox
{
    public readonly Jobs $jobs;

    /**
     * @param array{
     *   apiKey: string,
     *   baseUrl?: string,
     *   timeout?: float|int,
     *   maxRetries?: int,
     *   appName?: string|null,
     *   defaultHeaders?: array<string, string>,
     *   transport?: TransportInterface|callable|null
     * } $options
     */
    public function __construct(array $options)
    {
        $apiKey = isset($options['apiKey']) ? trim((string) $options['apiKey']) : '';
        if ($apiKey === '') {
            throw new InvalidArgumentException(
                'JobBox SDK requires an apiKey (set JOBBOX_API_KEY or pass apiKey).',
            );
        }

        $baseUrl = isset($options['baseUrl']) && is_string($options['baseUrl']) && $options['baseUrl'] !== ''
            ? rtrim($options['baseUrl'], '/')
            : 'https://api.getjobbox.com';
        $timeout = isset($options['timeout']) ? (float) $options['timeout'] : 30.0;
        $maxRetries = isset($options['maxRetries']) ? (int) $options['maxRetries'] : 2;
        $appName = $options['appName'] ?? null;
        $defaultHeaders = $options['defaultHeaders'] ?? [];
        $transport = $options['transport'] ?? new CurlTransport();

        $http = new Client(
            $apiKey,
            $baseUrl,
            $timeout,
            $maxRetries,
            Client::buildUserAgent(is_string($appName) ? $appName : null),
            $transport,
            is_array($defaultHeaders) ? $defaultHeaders : [],
        );

        $this->jobs = new Jobs($http);
    }
}
