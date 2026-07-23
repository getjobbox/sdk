<?php

declare(strict_types=1);

namespace GetJobBox\Tests;

use GetJobBox\Exceptions\JobBoxApiException;
use GetJobBox\JobBox;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class JobsTest extends TestCase
{
    /**
     * @param array<string, string>|null $headers
     * @return array{0: int, 1: array<string, string>, 2: string}
     */
    private static function jsonResponse(int $status, mixed $body, ?array $headers = null): array
    {
        return [
            $status,
            array_merge(['Content-Type' => 'application/json'], $headers ?? []),
            json_encode($body, JSON_THROW_ON_ERROR),
        ];
    }

    public function testSendsApiKeyAndUserAgent(): void
    {
        $calls = [];
        $transport = function (
            string $method,
            string $url,
            array $headers,
            ?string $body,
            float $timeout,
        ) use (&$calls): array {
            $calls[] = [$method, $url, $headers];
            $this->assertSame('jb_test_secret', $headers['X-JobBox-Api-Key'] ?? null);
            $this->assertMatchesRegularExpression('/^JobBoxPhpSDK\//', $headers['User-Agent'] ?? '');
            $this->assertSame(
                'https://api.getjobbox.com/api/v1/sdk/jobs?page=1&per_page=28',
                $url,
            );

            return self::jsonResponse(200, ['success' => true, 'data' => ['jobs' => [], 'total' => 0]]);
        };

        $client = new JobBox(['apiKey' => 'jb_test_secret', 'transport' => $transport]);
        $client->jobs->list();
        $this->assertCount(1, $calls);
    }

    public function testSerializesArrayFiltersAsCsvSnakeCase(): void
    {
        $transport = function (
            string $method,
            string $url,
            array $headers,
            ?string $body,
            float $timeout,
        ): array {
            $query = parse_url($url, PHP_URL_QUERY) ?? '';
            parse_str($query, $params);
            $this->assertSame('remote,hybrid', $params['work_mode'] ?? null);
            $this->assertSame('senior', $params['seniority_level'] ?? null);
            $this->assertSame('react', $params['search'] ?? null);

            return self::jsonResponse(200, [
                'success' => true,
                'data' => ['jobs' => [['id' => '1', 'title' => 'Engineer']], 'total' => 1],
            ]);
        };

        $client = new JobBox(['apiKey' => 'jb_test_secret', 'transport' => $transport]);
        $result = $client->jobs->list([
            'search' => 'react',
            'workMode' => ['remote', 'hybrid'],
            'seniorityLevel' => ['senior'],
        ]);

        $this->assertSame(1, $result['total']);
        $this->assertSame('Engineer', $result['jobs'][0]['title']);
        $this->assertSame(1, $result['page']);
        $this->assertSame(28, $result['perPage']);
    }

    public function testUnwrapsDataEnvelopeForGet(): void
    {
        $transport = function (
            string $method,
            string $url,
            array $headers,
            ?string $body,
            float $timeout,
        ): array {
            return self::jsonResponse(200, [
                'success' => true,
                'data' => ['job' => ['id' => 'abc', 'title' => 'Dev']],
            ]);
        };

        $client = new JobBox(['apiKey' => 'k', 'transport' => $transport]);
        $job = $client->jobs->get('abc')['job'];
        $this->assertSame('Dev', $job['title']);
    }

    public function testMapsApiErrorsToJobBoxApiException(): void
    {
        $transport = function (
            string $method,
            string $url,
            array $headers,
            ?string $body,
            float $timeout,
        ): array {
            return self::jsonResponse(401, [
                'success' => false,
                'code' => 'JB_APIKEY_401',
                'message' => 'Invalid or revoked API key',
            ]);
        };

        $client = new JobBox(['apiKey' => 'bad', 'transport' => $transport, 'maxRetries' => 0]);

        try {
            $client->jobs->list();
            $this->fail('Expected JobBoxApiException');
        } catch (JobBoxApiException $err) {
            $this->assertSame(401, $err->status);
            $this->assertSame('JB_APIKEY_401', $err->apiCode);
        }
    }

    public function testRetriesGetOn503ThenSucceeds(): void
    {
        $calls = 0;
        $transport = function (
            string $method,
            string $url,
            array $headers,
            ?string $body,
            float $timeout,
        ) use (&$calls): array {
            $calls++;
            if ($calls === 1) {
                return self::jsonResponse(
                    503,
                    ['success' => false, 'message' => 'unavailable'],
                    ['Retry-After' => '0'],
                );
            }

            return self::jsonResponse(200, ['success' => true, 'data' => ['categories' => []]]);
        };

        $client = new JobBox(['apiKey' => 'k', 'transport' => $transport, 'maxRetries' => 2]);
        $result = $client->jobs->categories();
        $this->assertSame([], $result['categories']);
        $this->assertSame(2, $calls);
    }

    public function testRequiresApiKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('apiKey');
        new JobBox(['apiKey' => '']);
    }
}
