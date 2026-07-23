# @getjobbox/sdk

Official JobBox Node.js / TypeScript SDK.

**Cross-language rules:** [`../STANDARD.md`](../STANDARD.md)

## Install

```bash
npm install @getjobbox/sdk
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

```ts
import { JobBox, JobBoxApiError } from '@getjobbox/sdk';

const jobbox = new JobBox({
  apiKey: process.env.JOBBOX_API_KEY!,
  // baseUrl: 'https://api.getjobbox.com',
});

const { jobs, total } = await jobbox.jobs.list({
  search: 'react',
  workMode: ['remote', 'hybrid'],
  seniorityLevel: ['senior'],
  page: 1,
  perPage: 28,
});

console.log(total, jobs[0]?.title);

try {
  const { job } = await jobbox.jobs.get('<uuid>');
  console.log(job.title);
} catch (err) {
  if (err instanceof JobBoxApiError) {
    console.error(err.status, err.code, err.message);
  }
  throw err;
}
```

## Jobs methods

| Method | API |
|--------|-----|
| `jobs.list(params)` | `GET /api/v1/sdk/jobs` |
| `jobs.get(id)` | `GET /api/v1/sdk/jobs/:id` |
| `jobs.similar(id)` | `GET /api/v1/sdk/jobs/:id/similar` |
| `jobs.categories()` | `GET /api/v1/sdk/jobs/meta/categories` |
| `jobs.countryOptions()` | `GET /api/v1/sdk/jobs/meta/country-options` |
| `jobs.opportunitiesCount()` | `GET /api/v1/sdk/jobs/meta/opportunities-count` |

### List filters (camelCase → wire snake_case)

| SDK option | Query param |
|------------|-------------|
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
| `page` / `perPage` | `page` / `per_page` |

## Client options

| Option | Default | Notes |
|--------|---------|-------|
| `apiKey` | required | Partner key |
| `baseUrl` | `https://api.getjobbox.com` | No trailing slash needed |
| `timeoutMs` | `30000` | |
| `maxRetries` | `2` | GET retries on 429/5xx |
| `appName` | — | Appended to User-Agent |
| `fetch` | `globalThis.fetch` | Injectable for tests |

## Development

```bash
cd sdk/node
npm install
npm run build
npm test
```
