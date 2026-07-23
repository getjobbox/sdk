# Metadata

Meta endpoints power filter UIs (category chips, country selectors) and opportunity badges without scanning the full job list.

| Method | HTTP |
|--------|------|
| `jobs.categories()` | `GET /api/v1/sdk/jobs/meta/categories` |
| `jobs.countryOptions()` / `country_options()` | `GET /api/v1/sdk/jobs/meta/country-options` |
| `jobs.opportunitiesCount()` / `opportunities_count()` | `GET /api/v1/sdk/jobs/meta/opportunities-count` |

---

## Categories

Returns catalog categories with `id`, `slug`, `label`, and optional `sort_order`.

=== "Node"

    ```ts
    const { categories } = await jobbox.jobs.categories();

    for (const cat of categories) {
      console.log(cat.slug, cat.label);
    }

    // Use a slug in list filters
    const hrJobs = await jobbox.jobs.list({ category: categories[0]?.slug });
    ```

=== "Python"

    ```python
    meta = jobbox.jobs.categories()
    for cat in meta["categories"]:
        print(cat["slug"], cat["label"])

    hr_jobs = jobbox.jobs.list(category=meta["categories"][0]["slug"])
    ```

=== "PHP"

    ```php
    $meta = $jobbox->jobs->categories();
    foreach ($meta['categories'] as $cat) {
        echo $cat['slug'] . ' ' . $cat['label'] . "\n";
    }

    $hrJobs = $jobbox->jobs->list([
        'category' => $meta['categories'][0]['slug'],
    ]);
    ```

Example apps lock a route to a category (e.g. `/hr` → `category: 'hr'`). Prefer resolving the slug from `categories()` rather than hard-coding when building dynamic UIs.

---

## Country options

Options for country filter controls. Shape may include an `options` array plus additional fields for forward compatibility.

=== "Node"

    ```ts
    const countryMeta = await jobbox.jobs.countryOptions();
    console.log(countryMeta);

    // After the user picks a country value:
    await jobbox.jobs.list({ country: 'NG' });
    ```

=== "Python"

    ```python
    country_meta = jobbox.jobs.country_options()
    print(country_meta)

    jobbox.jobs.list(country="NG")
    ```

=== "PHP"

    ```php
    $countryMeta = $jobbox->jobs->countryOptions();
    // Inspect $countryMeta for option values your UI needs

    $jobbox->jobs->list(['country' => 'NG']);
    ```

---

## Opportunities count

Lightweight total for opportunity listings (useful for badges / nav counts).

=== "Node"

    ```ts
    const count = await jobbox.jobs.opportunitiesCount();
    console.log(count.total);

    // List only opportunities
    const { jobs } = await jobbox.jobs.list({ opportunityOnly: true });
    ```

=== "Python"

    ```python
    count = jobbox.jobs.opportunities_count()
    print(count.get("total"))

    jobbox.jobs.list(opportunity_only=True)
    ```

=== "PHP"

    ```php
    $count = $jobbox->jobs->opportunitiesCount();
    echo ($count['total'] ?? '') . "\n";

    $jobbox->jobs->list(['opportunityOnly' => true]);
    ```

---

## Caching tip

Category and country metadata change infrequently. Cache them in memory or Redis on your server (TTL of minutes to hours) instead of calling meta endpoints on every page load.
