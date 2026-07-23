<?php

declare(strict_types=1);

namespace GetJobBox\Http;

use GetJobBox\Exceptions\JobBoxApiException;
use GetJobBox\Exceptions\JobBoxNetworkException;
use GetJobBox\Version;
use JsonException;
use Throwable;

final class Client
{
    /** @var callable|TransportInterface */
    private mixed $transport;

    /**
     * @param array<string, string> $defaultHeaders
     * @param callable|TransportInterface $transport
     */
    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl,
        private readonly float $timeoutSeconds,
        private readonly int $maxRetries,
        private readonly string $userAgent,
        mixed $transport,
        private readonly array $defaultHeaders,
    ) {
        $this->transport = $transport;
    }

    /**
     * @param array<string, mixed>|null $query
     * @param mixed|null $body
     */
    public function request(string $method, string $path, ?array $query = null, mixed $body = null): mixed
    {
        $url = rtrim($this->baseUrl, '/') . '/api/v1' . $path . self::buildQuery($query ?? []);
        $bodyString = null;
        $lastError = null;

        for ($attempt = 0; $attempt <= $this->maxRetries; $attempt++) {
            $headers = array_merge(
                [
                    'Accept' => 'application/json',
                    'X-JobBox-Api-Key' => $this->apiKey,
                    'User-Agent' => $this->userAgent,
                ],
                $this->defaultHeaders,
            );

            if ($body !== null) {
                $headers['Content-Type'] = 'application/json';
                try {
                    $bodyString = json_encode($body, JSON_THROW_ON_ERROR);
                } catch (JsonException $e) {
                    throw new JobBoxNetworkException('Failed to encode request body as JSON', $e, $e);
                }
            }

            try {
                [$status, $respHeaders, $raw] = $this->invokeTransport(
                    $method,
                    $url,
                    $headers,
                    $bodyString,
                    $this->timeoutSeconds,
                );

                $requestId = self::headerGet($respHeaders, 'x-request-id')
                    ?? self::headerGet($respHeaders, 'x-jobbox-request-id');

                $parsed = null;
                $text = $raw;
                if ($text !== '') {
                    try {
                        $parsed = json_decode($text, true, 512, JSON_THROW_ON_ERROR);
                    } catch (JsonException) {
                        $parsed = $text;
                    }
                }

                if (!($status >= 200 && $status < 300)) {
                    if ($attempt < $this->maxRetries && self::shouldRetry($method, $status)) {
                        $waitMs = self::backoffMs(
                            $attempt,
                            self::parseRetryAfterMs(self::headerGet($respHeaders, 'retry-after')),
                        );
                        usleep((int) ($waitMs * 1000));
                        continue;
                    }

                    $errBody = is_array($parsed) ? $parsed : null;
                    $message = (is_array($errBody) && isset($errBody['message']) && is_string($errBody['message']))
                        ? $errBody['message']
                        : "JobBox API request failed with status {$status}";
                    $code = (is_array($errBody) && isset($errBody['code']) && is_string($errBody['code']))
                        ? $errBody['code']
                        : null;

                    throw new JobBoxApiException(
                        $message,
                        $status,
                        $code,
                        $requestId,
                        $parsed,
                    );
                }

                if (is_array($parsed) && array_key_exists('data', $parsed)) {
                    return $parsed['data'];
                }

                return $parsed;
            } catch (JobBoxApiException $e) {
                throw $e;
            } catch (JobBoxNetworkException $e) {
                $lastError = $e;
                $isTimeout = stripos($e->getMessage(), 'timed out') !== false;
                if ($attempt < $this->maxRetries && strtoupper($method) === 'GET' && $isTimeout) {
                    usleep((int) (self::backoffMs($attempt, null) * 1000));
                    continue;
                }
                if ($isTimeout) {
                    throw $e;
                }
                if ($attempt < $this->maxRetries && strtoupper($method) === 'GET') {
                    usleep((int) (self::backoffMs($attempt, null) * 1000));
                    continue;
                }
                throw $e;
            } catch (Throwable $error) {
                $lastError = $error;
                $isTimeout = $error instanceof JobBoxNetworkException
                    && stripos($error->getMessage(), 'timed out') !== false;
                if ($attempt < $this->maxRetries && strtoupper($method) === 'GET' && $isTimeout) {
                    usleep((int) (self::backoffMs($attempt, null) * 1000));
                    continue;
                }
                if ($isTimeout) {
                    throw new JobBoxNetworkException('JobBox API request timed out', $error, $error);
                }
                if ($attempt < $this->maxRetries && strtoupper($method) === 'GET') {
                    usleep((int) (self::backoffMs($attempt, null) * 1000));
                    continue;
                }
                throw new JobBoxNetworkException(
                    $error->getMessage() !== '' ? $error->getMessage() : 'JobBox API network error',
                    $error,
                    $error,
                );
            }
        }

        throw new JobBoxNetworkException('JobBox API request failed after retries', $lastError);
    }

    /**
     * @param array<string, string> $headers
     * @return array{0: int, 1: array<string, string>, 2: string}
     */
    private function invokeTransport(
        string $method,
        string $url,
        array $headers,
        ?string $body,
        float $timeoutSeconds,
    ): array {
        if ($this->transport instanceof TransportInterface) {
            return $this->transport->request($method, $url, $headers, $body, $timeoutSeconds);
        }

        if (is_callable($this->transport)) {
            /** @var array{0: int, 1: array<string, string>, 2: string} $result */
            $result = ($this->transport)($method, $url, $headers, $body, $timeoutSeconds);

            return $result;
        }

        throw new JobBoxNetworkException('Invalid transport: expected TransportInterface or callable');
    }

    public static function toCsv(string|array|null $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (is_array($value)) {
            $parts = [];
            foreach ($value as $item) {
                $trimmed = trim((string) $item);
                if ($trimmed !== '') {
                    $parts[] = $trimmed;
                }
            }

            return $parts === [] ? null : implode(',', $parts);
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    /**
     * @param array<string, mixed> $params
     */
    public static function buildQuery(array $params): string
    {
        $items = [];
        foreach ($params as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $items[$key] = is_bool($value) ? ($value ? '1' : '0') : (string) $value;
        }
        if ($items === []) {
            return '';
        }

        return '?' . http_build_query($items, '', '&', PHP_QUERY_RFC3986);
    }

    public static function buildUserAgent(?string $appName = null): string
    {
        $base = 'JobBoxPhpSDK/' . Version::STRING;
        if ($appName !== null && trim($appName) !== '') {
            return $base . ' ' . trim($appName);
        }

        return $base;
    }

    private static function shouldRetry(string $method, int $status): bool
    {
        if (strtoupper($method) !== 'GET') {
            return false;
        }

        return $status === 429 || $status >= 500;
    }

    private static function parseRetryAfterMs(?string $header): ?float
    {
        if ($header === null || $header === '') {
            return null;
        }
        if (ctype_digit($header)) {
            return ((int) $header) * 1000.0;
        }
        $ts = strtotime($header);
        if ($ts === false) {
            return null;
        }

        return max(0.0, ($ts - time()) * 1000.0);
    }

    private static function backoffMs(int $attempt, ?float $retryAfterMs): float
    {
        if ($retryAfterMs !== null) {
            return $retryAfterMs;
        }
        $base = min(8000.0, 250.0 * (2 ** $attempt));
        $jitter = (mt_rand() / mt_getrandmax()) * 100.0;

        return $base + $jitter;
    }

    /**
     * @param array<string, string> $headers
     */
    private static function headerGet(array $headers, string $name): ?string
    {
        $lower = strtolower($name);
        foreach ($headers as $key => $value) {
            if (strtolower($key) === $lower) {
                return $value;
            }
        }

        return null;
    }
}
