# Pagination

`jobs.list` returns one page at a time. Use `page` and `perPage` / `per_page` to walk the result set.

| Param | Default | Notes |
|-------|---------|--------|
| `page` | `1` | 1-based page index |
| `perPage` / `per_page` | `28` | Page size |

The response includes `total` so you can compute how many pages remain.

---

## First page with a custom size

=== "Node"

    ```ts
    const { jobs, total, page, perPage } = await jobbox.jobs.list({
      search: 'designer',
      page: 1,
      perPage: 20,
    });

    console.log(`Showing ${jobs.length} of ${total} (page ${page})`);
    ```

=== "Python"

    ```python
    result = jobbox.jobs.list(search="designer", page=1, per_page=20)
    print(len(result["jobs"]), "of", result["total"], "page", result["page"])
    ```

=== "PHP"

    ```php
    $result = $jobbox->jobs->list([
        'search' => 'designer',
        'page' => 1,
        'perPage' => 20,
    ]);
    echo count($result['jobs']) . ' of ' . $result['total'] . "\n";
    ```

---

## Fetch the next page

=== "Node"

    ```ts
    const page1 = await jobbox.jobs.list({ search: 'react', page: 1, perPage: 28 });
    const page2 = await jobbox.jobs.list({ search: 'react', page: 2, perPage: 28 });
    ```

=== "Python"

    ```python
    page1 = jobbox.jobs.list(search="react", page=1, per_page=28)
    page2 = jobbox.jobs.list(search="react", page=2, per_page=28)
    ```

=== "PHP"

    ```php
    $page1 = $jobbox->jobs->list(['search' => 'react', 'page' => 1, 'perPage' => 28]);
    $page2 = $jobbox->jobs->list(['search' => 'react', 'page' => 2, 'perPage' => 28]);
    ```

Keep the **same filters** across pages; only change `page`.

---

## Loop until exhausted

=== "Node"

    ```ts
    async function listAllMatching(params: Omit<Parameters<typeof jobbox.jobs.list>[0], 'page'>) {
      const perPage = params.perPage ?? 28;
      let page = 1;
      const all = [];

      for (;;) {
        const { jobs, total } = await jobbox.jobs.list({ ...params, page, perPage });
        all.push(...jobs);
        if (all.length >= total || jobs.length === 0) break;
        page += 1;
      }

      return all;
    }

    const jobs = await listAllMatching({
      workMode: ['remote'],
      category: 'engineering',
      perPage: 50,
    });
    ```

=== "Python"

    ```python
    def list_all_matching(**params):
        per_page = params.pop("per_page", 28)
        page = 1
        all_jobs = []

        while True:
            result = jobbox.jobs.list(page=page, per_page=per_page, **params)
            batch = result["jobs"]
            all_jobs.extend(batch)
            if len(all_jobs) >= result["total"] or not batch:
                break
            page += 1

        return all_jobs

    jobs = list_all_matching(work_mode=["remote"], category="engineering", per_page=50)
    ```

=== "PHP"

    ```php
    function listAllMatching(JobBox $jobbox, array $params): array
    {
        $perPage = $params['perPage'] ?? 28;
        $page = 1;
        $all = [];

        while (true) {
            $result = $jobbox->jobs->list(array_merge($params, [
                'page' => $page,
                'perPage' => $perPage,
            ]));
            $batch = $result['jobs'];
            $all = array_merge($all, $batch);
            if (count($all) >= $result['total'] || $batch === []) {
                break;
            }
            $page++;
        }

        return $all;
    }

    $jobs = listAllMatching($jobbox, [
        'workMode' => ['remote'],
        'category' => 'engineering',
        'perPage' => 50,
    ]);
    ```

!!! warning "Be mindful of rate limits"
    Pulling every page in a tight loop can hit API quotas. Prefer UI pagination, or add a short delay between pages for bulk exports. The SDK retries `429` with backoff, but you should still design for quotas.

---

## UI pagination helpers

Given `total` and `perPage`:

```text
totalPages = ceil(total / perPage)
hasNext    = page * perPage < total
hasPrev    = page > 1
```

=== "Node"

    ```ts
    const { total, page, perPage } = await jobbox.jobs.list({ page: 3, perPage: 28 });
    const totalPages = Math.ceil(total / perPage);
    const hasNext = page * perPage < total;
    ```

=== "Python"

    ```python
    import math

    result = jobbox.jobs.list(page=3, per_page=28)
    total_pages = math.ceil(result["total"] / result["per_page"])
    has_next = result["page"] * result["per_page"] < result["total"]
    ```

=== "PHP"

    ```php
    $result = $jobbox->jobs->list(['page' => 3, 'perPage' => 28]);
    $totalPages = (int) ceil($result['total'] / $result['perPage']);
    $hasNext = $result['page'] * $result['perPage'] < $result['total'];
    ```
