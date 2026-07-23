# getjobbox/sdk

Official JobBox PHP SDK.

**Docs:** [JobBox SDK documentation](https://getjobbox.github.io/sdk/) (install, filters, examples)  
**Cross-language rules:** [`../STANDARD.md`](../STANDARD.md)

## Install

Distributed via **GitHub Releases** (zip asset). In your app `composer.json`:

```json
{
  "repositories": [
    {
      "type": "package",
      "package": {
        "name": "getjobbox/sdk",
        "version": "0.1.0",
        "dist": {
          "url": "https://github.com/getjobbox/sdk/releases/download/php-v0.1.0/getjobbox-sdk-php-0.1.0.zip",
          "type": "zip"
        },
        "autoload": {
          "psr-4": {
            "GetJobBox\\": "src/"
          }
        }
      }
    }
  ],
  "require": {
    "getjobbox/sdk": "0.1.0"
  }
}
```

Then:

```bash
composer update getjobbox/sdk
```

Bump `version` and the release download URL when upgrading.

From this repo (path / local):

```bash
cd php
composer install
```

## Authentication

1. Sign in to JobBox and create a key:

```http
POST /api/v1/api-keys
Authorization: Bearer <jwt>
Content-Type: application/json

{ "name": "my-app" }
```

2. Store the returned plaintext key (shown once) as `JOBBOX_API_KEY`. **Never commit keys.**

The SDK sends `X-JobBox-Api-Key` on every request.

## Quickstart

```php
<?php

use GetJobBox\Exceptions\JobBoxApiException;
use GetJobBox\JobBox;

$jobbox = new JobBox([
    'apiKey' => getenv('JOBBOX_API_KEY') ?: '',
    // 'baseUrl' => 'https://api.getjobbox.com',
]);

$result = $jobbox->jobs->list([
    'search' => 'react',
    'workMode' => ['remote', 'hybrid'],
    'seniorityLevel' => ['senior'],
    'page' => 1,
    'perPage' => 28,
]);

echo $result['total'] . "\n";
if ($result['jobs'] !== []) {
    echo $result['jobs'][0]['title'] . "\n";
}

try {
    $job = $jobbox->jobs->get('<uuid>')['job'];
    echo $job['title'] . "\n";
} catch (JobBoxApiException $err) {
    echo $err->status . ' ' . ($err->apiCode ?? '') . ' ' . $err->getMessage() . "\n";
    throw $err;
}
```

## Jobs methods

| Method | API |
|--------|-----|
| `jobs->list($params)` | `GET /api/v1/sdk/jobs` |
| `jobs->get($id)` | `GET /api/v1/sdk/jobs/:id` |
| `jobs->similar($id)` | `GET /api/v1/sdk/jobs/:id/similar` |
| `jobs->categories()` | `GET /api/v1/sdk/jobs/meta/categories` |
| `jobs->countryOptions()` | `GET /api/v1/sdk/jobs/meta/country-options` |
| `jobs->opportunitiesCount()` | `GET /api/v1/sdk/jobs/meta/opportunities-count` |

### List filters (camelCase → wire snake_case)

| SDK key | Query param |
|---------|-------------|
| `search` | `search` |
| `location` | `location` |
| `country` | `country` |
| `workMode` | `work_mode` (CSV) |
| `seniorityLevel` | `seniority_level` (CSV) |
| `employmentTypes` | `employment_types` (CSV) |
| `benefitFilters` | `benefit_filters` (CSV) |
| `companies` | `companies` (CSV) |
| `dateFrom` / `dateTo` | `date_from` / `date_to` |
| `salaryMin` / `salaryMax` | `salary_min` / `salary_max` |
| `category` | `category` |
| `compensationType` | `compensation_type` (`job` \| `gig`) |
| `applicationMode` | `application_mode` (CSV) |
| `opportunityOnly` | `opportunity_only` |
| `page` / `perPage` | `page` / `per_page` (defaults `1` / `28`) |

## Client options

| Option | Default | Notes |
|--------|---------|-------|
| `apiKey` | required | Partner key |
| `baseUrl` | `https://api.getjobbox.com` | No trailing slash needed |
| `timeout` | `30.0` | Seconds |
| `maxRetries` | `2` | GET retries on 429/5xx |
| `appName` | — | Appended to User-Agent |
| `transport` | cURL | Injectable `TransportInterface` or callable for tests |
| `defaultHeaders` | `[]` | Extra headers |

## Errors

- `JobBoxApiException` — non-2xx API response (`status`, `apiCode`, `requestId`, `body`)
- `JobBoxNetworkException` — transport / timeout (`causeError`)

## Examples

- **Laravel job board (PHP SDK):** [`../examples/laravel`](../examples/laravel)
- **CodeIgniter 4 job board (PHP SDK):** [`../examples/codeigniter`](../examples/codeigniter)
- Vue 3 job board: [`../examples/vue`](../examples/vue)
- React job board: [`../examples/react`](../examples/react)
- Angular job board: [`../examples/angular`](../examples/angular)

## Development

```bash
cd php
composer install
composer test
```

Requires **PHP ≥ 8.1** with `ext-curl` and `ext-json`. Zero runtime Composer dependencies.
