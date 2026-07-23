# getjobbox

Official JobBox Python SDK.

**Docs:** [JobBox SDK documentation](https://getjobbox.github.io/sdk/) (install, filters, examples)  
**Cross-language rules:** [`../STANDARD.md`](../STANDARD.md)

## Install

Distributed via **GitHub Releases** (wheel / sdist). Example for `0.1.0`:

```bash
pip install "https://github.com/getjobbox/sdk/releases/download/python-v0.1.0/getjobbox-0.1.0-py3-none-any.whl"
```

From this repo (editable / local):

```bash
pip install -e .
# or with test deps:
pip install -e ".[dev]"
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

```python
import os
from getjobbox import JobBox, JobBoxApiError

jobbox = JobBox(
    api_key=os.environ["JOBBOX_API_KEY"],
    # base_url="https://api.getjobbox.com",
)

result = jobbox.jobs.list(
    search="react",
    work_mode=["remote", "hybrid"],
    seniority_level=["senior"],
    page=1,
    per_page=28,
)
print(result["total"], result["jobs"][0]["title"] if result["jobs"] else None)

try:
    job = jobbox.jobs.get("<uuid>")["job"]
    print(job["title"])
except JobBoxApiError as err:
    print(err.status, err.code, err)
    raise
```

## Jobs methods

| Method | API |
|--------|-----|
| `jobs.list(**params)` | `GET /api/v1/sdk/jobs` |
| `jobs.get(id)` | `GET /api/v1/sdk/jobs/:id` |
| `jobs.similar(id)` | `GET /api/v1/sdk/jobs/:id/similar` |
| `jobs.categories()` | `GET /api/v1/sdk/jobs/meta/categories` |
| `jobs.country_options()` | `GET /api/v1/sdk/jobs/meta/country-options` |
| `jobs.opportunities_count()` | `GET /api/v1/sdk/jobs/meta/opportunities-count` |

### List filters (snake_case matching wire)

| SDK kwarg | Query param |
|-----------|-------------|
| `search` | `search` |
| `location` | `location` |
| `country` | `country` |
| `work_mode` | `work_mode` (CSV) |
| `seniority_level` | `seniority_level` (CSV) |
| `employment_types` | `employment_types` (CSV) |
| `benefit_filters` | `benefit_filters` (CSV) |
| `companies` | `companies` (CSV) |
| `date_from` / `date_to` | `date_from` / `date_to` |
| `salary_min` / `salary_max` | `salary_min` / `salary_max` |
| `category` | `category` |
| `compensation_type` | `compensation_type` (`job` \| `gig`) |
| `application_mode` | `application_mode` (CSV) |
| `opportunity_only` | `opportunity_only` |
| `page` / `per_page` | `page` / `per_page` (defaults `1` / `28`) |

## Client options

| Option | Default | Notes |
|--------|---------|-------|
| `api_key` | required | Partner key |
| `base_url` | `https://api.getjobbox.com` | No trailing slash needed |
| `timeout` | `30.0` | Seconds |
| `max_retries` | `2` | GET retries on 429/5xx |
| `app_name` | — | Appended to User-Agent |
| `transport` | stdlib `urllib` | Injectable for tests |
| `default_headers` | `{}` | Extra headers |

## Errors

- `JobBoxApiError` — non-2xx API response (`status`, `code`, `request_id`, `body`)
- `JobBoxNetworkError` — transport / timeout (`cause_error`)

## Examples

- Vue 3 job board: [`../examples/vue`](../examples/vue)
- React job board: [`../examples/react`](../examples/react)
- Angular job board: [`../examples/angular`](../examples/angular)

## Development

```bash
cd python
python -m pip install -e ".[dev]"
pytest -q
```

Requires **Python ≥ 3.10**. Zero runtime dependencies.
