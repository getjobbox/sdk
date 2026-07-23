<?php

declare(strict_types=1);

namespace GetJobBox\Http;

use GetJobBox\Exceptions\JobBoxNetworkException;
use RuntimeException;

/**
 * Default cURL-based transport (zero Composer runtime deps).
 */
final class CurlTransport implements TransportInterface
{
    public function request(
        string $method,
        string $url,
        array $headers,
        ?string $body,
        float $timeoutSeconds,
    ): array {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('JobBox SDK requires the PHP cURL extension.');
        }

        $ch = curl_init($url);
        if ($ch === false) {
            throw new JobBoxNetworkException('Failed to initialize cURL');
        }

        $headerLines = [];
        foreach ($headers as $key => $value) {
            $headerLines[] = $key . ': ' . $value;
        }

        $responseHeaders = [];
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => (int) max(1, ceil($timeoutSeconds)),
            CURLOPT_CONNECTTIMEOUT => (int) max(1, ceil(min($timeoutSeconds, 10.0))),
            CURLOPT_HTTPHEADER => $headerLines,
            CURLOPT_HEADERFUNCTION => static function ($curl, string $headerLine) use (&$responseHeaders): int {
                $len = strlen($headerLine);
                $parts = explode(':', $headerLine, 2);
                if (count($parts) === 2) {
                    $name = trim($parts[0]);
                    $value = trim($parts[1]);
                    if ($name !== '') {
                        $responseHeaders[$name] = $value;
                    }
                }

                return $len;
            },
        ]);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $raw = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false) {
            $message = $error !== '' ? $error : 'JobBox API network error';
            if ($errno === CURLE_OPERATION_TIMEDOUT || stripos($message, 'timed out') !== false) {
                throw new JobBoxNetworkException('JobBox API request timed out', $error !== '' ? $error : $errno);
            }
            throw new JobBoxNetworkException($message, $error !== '' ? $error : $errno);
        }

        return [$status, $responseHeaders, (string) $raw];
    }
}
