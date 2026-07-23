# Jobs API reference

All Jobs methods are under `client.jobs`. Base path: `/api/v1/sdk/jobs`.

---

## Methods

| SDK method | HTTP | Description |
|------------|------|-------------|
| `list(params?)` | `GET /sdk/jobs` | Paginated catalog search |
| `get(id)` | `GET /sdk/jobs/:id` | Single job by UUID |
| `similar(id)` | `GET /sdk/jobs/:id/similar` | Related jobs |
| `categories()` | `GET /sdk/jobs/meta/categories` | Category list |
| `countryOptions()` / `country_options()` | `GET /sdk/jobs/meta/country-options` | Country filter options |
| `opportunitiesCount()` / `opportunities_count()` | `GET /sdk/jobs/meta/opportunities-count` | Opportunity total |

Python uses snake_case for `country_options` and `opportunities_count`. Node and PHP use camelCase.

---

## `list`

=== "Node"

    ```ts
    jobs.list(params?: JobListParams): Promise<JobListResult>
    // JobListResult: { jobs, total, page, perPage }
    ```

=== "Python"

    ```python
    jobs.list(**kwargs) -> dict  # jobs, total, page, per_page
    ```

=== "PHP"

    ```php
    jobs->list(?array $params = []): array  // jobs, total, page, perPage
    ```

Filter parameters: [Filters](filters.md). Guides: [List jobs](../guides/list-jobs.md), [Search and filters](../guides/search-and-filters.md).

---

## `get`

=== "Node"

    ```ts
    jobs.get(id: string): Promise<{ job: Job }>
    ```

=== "Python"

    ```python
    jobs.get(job_id: str) -> dict  # {"job": {...}}
    ```

=== "PHP"

    ```php
    jobs->get(string $id): array  // ['job' => [...]]
    ```

Guide: [Get a job](../guides/get-job.md).

---

## `similar`

=== "Node"

    ```ts
    jobs.similar(id: string): Promise<{ jobs: Job[] }>
    ```

=== "Python"

    ```python
    jobs.similar(job_id: str) -> dict  # {"jobs": [...]}
    ```

=== "PHP"

    ```php
    jobs->similar(string $id): array
    ```

Guide: [Similar jobs](../guides/similar-jobs.md).

---

## Meta methods

=== "Node"

    ```ts
    jobs.categories(): Promise<CategoriesResult>
    jobs.countryOptions(): Promise<CountryOptionsResult>
    jobs.opportunitiesCount(): Promise<OpportunitiesCountResult>
    ```

=== "Python"

    ```python
    jobs.categories() -> dict
    jobs.country_options() -> dict
    jobs.opportunities_count() -> dict
    ```

=== "PHP"

    ```php
    jobs->categories(): array
    jobs->countryOptions(): array
    jobs->opportunitiesCount(): array
    ```

Guide: [Metadata](../guides/metadata.md).

---

## Out of scope (v1)

- API key CRUD (`POST /api/v1/api-keys` with JWT) — use the product UI or authenticated REST, not the Jobs SDK
- Unauthenticated public website routes (`/jobs/public*`)
- Applications or other future resources
