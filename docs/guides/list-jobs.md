# List jobs

`jobs.list` calls `GET /api/v1/sdk/jobs` and returns a page of catalog jobs plus pagination metadata.

---

## Basic call

=== "Node"

    ```ts
    const { jobs, total, page, perPage } = await jobbox.jobs.list();

    for (const job of jobs) {
      console.log(job.id, job.title, job.company);
    }
    ```

=== "Python"

    ```python
    result = jobbox.jobs.list()
    jobs = result["jobs"]
    total = result["total"]

    for job in jobs:
        print(job["id"], job.get("title"), job.get("company"))
    ```

=== "PHP"

    ```php
    $result = $jobbox->jobs->list();
    $jobs = $result['jobs'];
    $total = $result['total'];

    foreach ($jobs as $job) {
        echo $job['id'] . ' ' . ($job['title'] ?? '') . "\n";
    }
    ```

Defaults: `page = 1`, `per_page = 28`.

---

## Response shape

| Field | Type | Description |
|-------|------|-------------|
| `jobs` | array | Job objects for this page |
| `total` | number | Total matching jobs across all pages |
| `page` | number | Current page (Node: `page`) |
| `perPage` / `per_page` | number | Page size |

!!! note "Python / PHP keys"
    Python and PHP return wire-aligned keys for pagination where applicable (`per_page` in Python). Node maps to camelCase: `perPage`.

### Job object (common fields)

Jobs are typed loosely for forward compatibility. Typical fields include:

| Field | Description |
|-------|-------------|
| `id` | Job UUID |
| `title` | Role title |
| `company` | Company name |
| `location` | Location string |
| `category` | Category slug / label |
| `work_mode` | e.g. remote / hybrid / onsite |
| `seniority_level` | Seniority |
| `employment_type` | Employment type |
| `status` | Listing status |

Additional fields may appear as the API evolves; treat unknown keys as opaque.

---

## Envelope unwrapping

The HTTP API returns:

```json
{
  "success": true,
  "data": {
    "jobs": [ /* … */ ],
    "total": 120,
    "page": 1,
    "per_page": 28
  }
}
```

SDKs return the inner `data` object only. You never need to check `success` on the happy path — failures throw typed errors (see [Error handling](error-handling.md)).

---

## With filters

Pass any combination of [list filters](search-and-filters.md):

=== "Node"

    ```ts
    const { jobs, total } = await jobbox.jobs.list({
      search: 'product designer',
      location: 'Lagos',
      workMode: ['remote'],
      page: 1,
      perPage: 20,
    });
    ```

=== "Python"

    ```python
    result = jobbox.jobs.list(
        search="product designer",
        location="Lagos",
        work_mode=["remote"],
        page=1,
        per_page=20,
    )
    ```

=== "PHP"

    ```php
    $result = $jobbox->jobs->list([
        'search' => 'product designer',
        'location' => 'Lagos',
        'workMode' => ['remote'],
        'page' => 1,
        'perPage' => 20,
    ]);
    ```

---

## Related

- [Search and filters](search-and-filters.md) — every filter with examples  
- [Pagination](pagination.md)  
- [Filters reference](../reference/filters.md)
