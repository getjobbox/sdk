# Filters reference

Query parameters for `jobs.list`. Multi-value filters are sent as **CSV** on the wire.

---

## Parameter map

| Node / PHP (camelCase) | Python / wire (snake_case) | Multi | Notes |
|------------------------|----------------------------|-------|--------|
| `search` | `search` | | Text search |
| `location` | `location` | | Free-text location |
| `country` | `country` | | Country code / value from `countryOptions` |
| `workMode` | `work_mode` | CSV | e.g. remote, hybrid, onsite |
| `seniorityLevel` | `seniority_level` | CSV | |
| `employmentTypes` | `employment_types` | CSV | |
| `benefitFilters` | `benefit_filters` | CSV | |
| `companies` | `companies` | CSV | Company names |
| `dateFrom` | `date_from` | | Prefer `YYYY-MM-DD` |
| `dateTo` | `date_to` | | Prefer `YYYY-MM-DD` |
| `salaryMin` | `salary_min` | | Number |
| `salaryMax` | `salary_max` | | Number |
| `category` | `category` | | Slug from `categories()` |
| `compensationType` | `compensation_type` | | `job` \| `gig` |
| `applicationMode` | `application_mode` | CSV | |
| `opportunityOnly` | `opportunity_only` | | boolean / `true`/`false`/`1`/`0` |
| `page` | `page` | | Default `1` |
| `perPage` | `per_page` | | Default `28` |

---

## Types (Node)

```ts
interface JobListParams {
  search?: string;
  location?: string;
  country?: string;
  workMode?: string | string[];
  seniorityLevel?: string | string[];
  employmentTypes?: string | string[];
  benefitFilters?: string | string[];
  companies?: string | string[];
  dateFrom?: string;
  dateTo?: string;
  salaryMin?: number;
  salaryMax?: number;
  category?: string;
  compensationType?: 'job' | 'gig';
  applicationMode?: string | string[];
  opportunityOnly?: boolean | 'true' | '1' | 'false' | '0';
  page?: number;
  perPage?: number;
}
```

---

## Serialization example

=== "Node"

    ```ts
    await jobbox.jobs.list({
      workMode: ['remote', 'hybrid'],
      seniorityLevel: ['senior'],
    });
    // → ?work_mode=remote,hybrid&seniority_level=senior
    ```

=== "Python"

    ```python
    jobbox.jobs.list(work_mode=["remote", "hybrid"], seniority_level=["senior"])
    # → ?work_mode=remote,hybrid&seniority_level=senior
    ```

=== "PHP"

    ```php
    $jobbox->jobs->list([
        'workMode' => ['remote', 'hybrid'],
        'seniorityLevel' => ['senior'],
    ]);
    // → ?work_mode=remote,hybrid&seniority_level=senior
    ```

---

## Guides

- [Search and filters](../guides/search-and-filters.md) — detailed examples per filter  
- [Pagination](../guides/pagination.md)
