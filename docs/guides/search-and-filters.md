# Search and filters

This guide covers every `jobs.list` filter with focused examples in Node, Python, and PHP. For a compact table, see [Filters reference](../reference/filters.md).

!!! tip "CSV on the wire"
    Multi-value filters accept a string or an array/list. Arrays are serialized as comma-separated values (e.g. `work_mode=remote,hybrid`).

!!! tip "Naming"
    Node and PHP use **camelCase** (`workMode`). Python uses **snake_case** (`work_mode`), matching the HTTP query params.

---

## Text search

Full-text style search over the catalog (titles, roles, and related fields as implemented by the API).

=== "Node"

    ```ts
    const { jobs, total } = await jobbox.jobs.list({
      search: 'react engineer',
    });
    ```

=== "Python"

    ```python
    result = jobbox.jobs.list(search="react engineer")
    ```

=== "PHP"

    ```php
    $result = $jobbox->jobs->list(['search' => 'react engineer']);
    ```

Combine with other filters — search narrows the set; filters refine it further.

---

## Location and country

=== "Node"

    ```ts
    // Free-text location
    await jobbox.jobs.list({ location: 'Berlin' });

    // Country filter (use codes/labels from countryOptions when building UIs)
    await jobbox.jobs.list({ country: 'NG' });

    // Both
    await jobbox.jobs.list({
      location: 'Lagos',
      country: 'NG',
    });
    ```

=== "Python"

    ```python
    jobbox.jobs.list(location="Berlin")
    jobbox.jobs.list(country="NG")
    jobbox.jobs.list(location="Lagos", country="NG")
    ```

=== "PHP"

    ```php
    $jobbox->jobs->list(['location' => 'Berlin']);
    $jobbox->jobs->list(['country' => 'NG']);
    $jobbox->jobs->list(['location' => 'Lagos', 'country' => 'NG']);
    ```

Load country choices with [`jobs.countryOptions()`](metadata.md#country-options).

---

## Work mode

Typical values include `remote`, `hybrid`, and `onsite` (exact catalog values may vary).

=== "Node"

    ```ts
    // Single value
    await jobbox.jobs.list({ workMode: 'remote' });

    // Multiple → work_mode=remote,hybrid
    await jobbox.jobs.list({
      workMode: ['remote', 'hybrid'],
    });
    ```

=== "Python"

    ```python
    jobbox.jobs.list(work_mode="remote")
    jobbox.jobs.list(work_mode=["remote", "hybrid"])
    ```

=== "PHP"

    ```php
    $jobbox->jobs->list(['workMode' => 'remote']);
    $jobbox->jobs->list(['workMode' => ['remote', 'hybrid']]);
    ```

---

## Seniority level

=== "Node"

    ```ts
    await jobbox.jobs.list({
      seniorityLevel: ['junior', 'mid', 'senior'],
    });

    // Senior only
    await jobbox.jobs.list({ seniorityLevel: ['senior'] });
    ```

=== "Python"

    ```python
    jobbox.jobs.list(seniority_level=["junior", "mid", "senior"])
    jobbox.jobs.list(seniority_level=["senior"])
    ```

=== "PHP"

    ```php
    $jobbox->jobs->list(['seniorityLevel' => ['junior', 'mid', 'senior']]);
    $jobbox->jobs->list(['seniorityLevel' => ['senior']]);
    ```

---

## Employment types

=== "Node"

    ```ts
    await jobbox.jobs.list({
      employmentTypes: ['full_time', 'contract'],
    });
    ```

=== "Python"

    ```python
    jobbox.jobs.list(employment_types=["full_time", "contract"])
    ```

=== "PHP"

    ```php
    $jobbox->jobs->list([
        'employmentTypes' => ['full_time', 'contract'],
    ]);
    ```

---

## Benefit filters

Filter listings by benefit tags supported by the catalog.

=== "Node"

    ```ts
    await jobbox.jobs.list({
      benefitFilters: ['health_insurance', 'equity'],
    });
    ```

=== "Python"

    ```python
    jobbox.jobs.list(benefit_filters=["health_insurance", "equity"])
    ```

=== "PHP"

    ```php
    $jobbox->jobs->list([
        'benefitFilters' => ['health_insurance', 'equity'],
    ]);
    ```

---

## Companies

Restrict results to one or more company names (CSV on the wire).

=== "Node"

    ```ts
    await jobbox.jobs.list({
      companies: ['Acme', 'Globex'],
    });
    ```

=== "Python"

    ```python
    jobbox.jobs.list(companies=["Acme", "Globex"])
    ```

=== "PHP"

    ```php
    $jobbox->jobs->list(['companies' => ['Acme', 'Globex']]);
    ```

---

## Date range

Filter by posting / listing date window. Use ISO date strings (`YYYY-MM-DD`) unless your integration docs specify otherwise.

=== "Node"

    ```ts
    await jobbox.jobs.list({
      dateFrom: '2026-01-01',
      dateTo: '2026-07-23',
    });
    ```

=== "Python"

    ```python
    jobbox.jobs.list(date_from="2026-01-01", date_to="2026-07-23")
    ```

=== "PHP"

    ```php
    $jobbox->jobs->list([
        'dateFrom' => '2026-01-01',
        'dateTo' => '2026-07-23',
    ]);
    ```

---

## Salary range

Numeric minimum and/or maximum. Units follow the catalog (typically the job’s posted currency).

=== "Node"

    ```ts
    await jobbox.jobs.list({
      salaryMin: 80000,
      salaryMax: 150000,
    });

    // Open-ended minimum
    await jobbox.jobs.list({ salaryMin: 100000 });
    ```

=== "Python"

    ```python
    jobbox.jobs.list(salary_min=80_000, salary_max=150_000)
    jobbox.jobs.list(salary_min=100_000)
    ```

=== "PHP"

    ```php
    $jobbox->jobs->list([
        'salaryMin' => 80000,
        'salaryMax' => 150000,
    ]);
    $jobbox->jobs->list(['salaryMin' => 100000]);
    ```

---

## Category

Pass a category slug (e.g. `hr`, `engineering`). Discover slugs via [`jobs.categories()`](metadata.md#categories).

=== "Node"

    ```ts
    const { categories } = await jobbox.jobs.categories();
    // Pick a slug from categories[i].slug

    await jobbox.jobs.list({ category: 'hr' });
    ```

=== "Python"

    ```python
    meta = jobbox.jobs.categories()
    # meta["categories"][i]["slug"]

    jobbox.jobs.list(category="hr")
    ```

=== "PHP"

    ```php
    $meta = $jobbox->jobs->categories();
    // $meta['categories'][i]['slug']

    $jobbox->jobs->list(['category' => 'hr']);
    ```

---

## Compensation type

Distinguish traditional jobs from gigs.

=== "Node"

    ```ts
    await jobbox.jobs.list({ compensationType: 'job' });
    await jobbox.jobs.list({ compensationType: 'gig' });
    ```

=== "Python"

    ```python
    jobbox.jobs.list(compensation_type="job")
    jobbox.jobs.list(compensation_type="gig")
    ```

=== "PHP"

    ```php
    $jobbox->jobs->list(['compensationType' => 'job']);
    $jobbox->jobs->list(['compensationType' => 'gig']);
    ```

Allowed values: `job` | `gig`.

---

## Application mode

Filter by how candidates apply (values are catalog-defined; CSV when multiple).

=== "Node"

    ```ts
    await jobbox.jobs.list({
      applicationMode: ['external', 'easy_apply'],
    });
    ```

=== "Python"

    ```python
    jobbox.jobs.list(application_mode=["external", "easy_apply"])
    ```

=== "PHP"

    ```php
    $jobbox->jobs->list([
        'applicationMode' => ['external', 'easy_apply'],
    ]);
    ```

---

## Opportunity-only

Limit results to opportunity listings when supported by the catalog.

=== "Node"

    ```ts
    await jobbox.jobs.list({ opportunityOnly: true });
    // Also accepted: 'true' | '1' | false | 'false' | '0'
    ```

=== "Python"

    ```python
    jobbox.jobs.list(opportunity_only=True)
    ```

=== "PHP"

    ```php
    $jobbox->jobs->list(['opportunityOnly' => true]);
    ```

Use [`jobs.opportunitiesCount()`](metadata.md#opportunities-count) for a total without listing every row.

---

## Advanced search (combined)

Kitchen-sink query: text search plus several filters and pagination.

=== "Node"

    ```ts
    const { jobs, total, page, perPage } = await jobbox.jobs.list({
      search: 'frontend',
      location: 'Remote',
      country: 'US',
      workMode: ['remote', 'hybrid'],
      seniorityLevel: ['mid', 'senior'],
      employmentTypes: ['full_time'],
      benefitFilters: ['health_insurance'],
      companies: ['Acme'],
      dateFrom: '2026-01-01',
      dateTo: '2026-07-23',
      salaryMin: 90000,
      salaryMax: 180000,
      category: 'engineering',
      compensationType: 'job',
      applicationMode: ['external'],
      opportunityOnly: false,
      page: 1,
      perPage: 28,
    });

    console.log({ total, page, perPage, count: jobs.length });
    ```

=== "Python"

    ```python
    result = jobbox.jobs.list(
        search="frontend",
        location="Remote",
        country="US",
        work_mode=["remote", "hybrid"],
        seniority_level=["mid", "senior"],
        employment_types=["full_time"],
        benefit_filters=["health_insurance"],
        companies=["Acme"],
        date_from="2026-01-01",
        date_to="2026-07-23",
        salary_min=90_000,
        salary_max=180_000,
        category="engineering",
        compensation_type="job",
        application_mode=["external"],
        opportunity_only=False,
        page=1,
        per_page=28,
    )
    print(result["total"], len(result["jobs"]))
    ```

=== "PHP"

    ```php
    $result = $jobbox->jobs->list([
        'search' => 'frontend',
        'location' => 'Remote',
        'country' => 'US',
        'workMode' => ['remote', 'hybrid'],
        'seniorityLevel' => ['mid', 'senior'],
        'employmentTypes' => ['full_time'],
        'benefitFilters' => ['health_insurance'],
        'companies' => ['Acme'],
        'dateFrom' => '2026-01-01',
        'dateTo' => '2026-07-23',
        'salaryMin' => 90000,
        'salaryMax' => 180000,
        'category' => 'engineering',
        'compensationType' => 'job',
        'applicationMode' => ['external'],
        'opportunityOnly' => false,
        'page' => 1,
        'perPage' => 28,
    ]);

    echo $result['total'] . ' ' . count($result['jobs']) . "\n";
    ```

Wire query (illustrative):

```http
GET /api/v1/sdk/jobs?search=frontend&location=Remote&country=US&work_mode=remote,hybrid&seniority_level=mid,senior&…&page=1&per_page=28
X-JobBox-Api-Key: <key>
```

---

## Practical recipes

### Remote senior roles in a category

=== "Node"

    ```ts
    await jobbox.jobs.list({
      category: 'engineering',
      workMode: ['remote'],
      seniorityLevel: ['senior'],
      compensationType: 'job',
    });
    ```

=== "Python"

    ```python
    jobbox.jobs.list(
        category="engineering",
        work_mode=["remote"],
        seniority_level=["senior"],
        compensation_type="job",
    )
    ```

=== "PHP"

    ```php
    $jobbox->jobs->list([
        'category' => 'engineering',
        'workMode' => ['remote'],
        'seniorityLevel' => ['senior'],
        'compensationType' => 'job',
    ]);
    ```

### Gigs posted this month

=== "Node"

    ```ts
    await jobbox.jobs.list({
      compensationType: 'gig',
      dateFrom: '2026-07-01',
      dateTo: '2026-07-31',
      page: 1,
      perPage: 50,
    });
    ```

=== "Python"

    ```python
    jobbox.jobs.list(
        compensation_type="gig",
        date_from="2026-07-01",
        date_to="2026-07-31",
        page=1,
        per_page=50,
    )
    ```

=== "PHP"

    ```php
    $jobbox->jobs->list([
        'compensationType' => 'gig',
        'dateFrom' => '2026-07-01',
        'dateTo' => '2026-07-31',
        'page' => 1,
        'perPage' => 50,
    ]);
    ```

### Salary band + location search

=== "Node"

    ```ts
    await jobbox.jobs.list({
      search: 'backend',
      location: 'London',
      salaryMin: 70000,
      salaryMax: 120000,
      workMode: ['hybrid', 'onsite'],
    });
    ```

=== "Python"

    ```python
    jobbox.jobs.list(
        search="backend",
        location="London",
        salary_min=70_000,
        salary_max=120_000,
        work_mode=["hybrid", "onsite"],
    )
    ```

=== "PHP"

    ```php
    $jobbox->jobs->list([
        'search' => 'backend',
        'location' => 'London',
        'salaryMin' => 70000,
        'salaryMax' => 120000,
        'workMode' => ['hybrid', 'onsite'],
    ]);
    ```

---

## Related

- [List jobs](list-jobs.md)  
- [Pagination](pagination.md)  
- [Filters reference](../reference/filters.md)
