# Quickstart

End-to-end: install → set key → list jobs → fetch one job. Assumes you already completed [installation](installation.md) and have `JOBBOX_API_KEY` set.

---

## 1. Create the client

=== "Node"

    ```ts
    import { JobBox, JobBoxApiError } from '@getjobbox/sdk';

    const jobbox = new JobBox({
      apiKey: process.env.JOBBOX_API_KEY!,
      // baseUrl: 'https://api.getjobbox.com',
    });
    ```

=== "Python"

    ```python
    import os
    from getjobbox import JobBox, JobBoxApiError

    jobbox = JobBox(
        api_key=os.environ["JOBBOX_API_KEY"],
        # base_url="https://api.getjobbox.com",
    )
    ```

=== "PHP"

    ```php
    <?php
    use GetJobBox\Exceptions\JobBoxApiException;
    use GetJobBox\JobBox;

    $jobbox = new JobBox([
        'apiKey' => getenv('JOBBOX_API_KEY') ?: '',
        // 'baseUrl' => 'https://api.getjobbox.com',
    ]);
    ```

---

## 2. List jobs with a simple search

=== "Node"

    ```ts
    const { jobs, total, page, perPage } = await jobbox.jobs.list({
      search: 'react',
      workMode: ['remote', 'hybrid'],
      seniorityLevel: ['senior'],
      page: 1,
      perPage: 28,
    });

    console.log(`Found ${total} jobs (page ${page}, ${perPage} per page)`);
    console.log(jobs[0]?.title, jobs[0]?.company);
    ```

=== "Python"

    ```python
    result = jobbox.jobs.list(
        search="react",
        work_mode=["remote", "hybrid"],
        seniority_level=["senior"],
        page=1,
        per_page=28,
    )

    print(f"Found {result['total']} jobs")
    if result["jobs"]:
        print(result["jobs"][0]["title"], result["jobs"][0].get("company"))
    ```

=== "PHP"

    ```php
    $result = $jobbox->jobs->list([
        'search' => 'react',
        'workMode' => ['remote', 'hybrid'],
        'seniorityLevel' => ['senior'],
        'page' => 1,
        'perPage' => 28,
    ]);

    echo "Found {$result['total']} jobs\n";
    if ($result['jobs'] !== []) {
        echo $result['jobs'][0]['title'] . "\n";
    }
    ```

The SDK unwraps the API success envelope (`{ success, data }`) so you receive the inner `data` payload directly.

---

## 3. Fetch a single job

=== "Node"

    ```ts
    try {
      const { job } = await jobbox.jobs.get('<job-uuid>');
      console.log(job.title, job.location);
    } catch (err) {
      if (err instanceof JobBoxApiError) {
        console.error(err.status, err.code, err.message);
      }
      throw err;
    }
    ```

=== "Python"

    ```python
    try:
        job = jobbox.jobs.get("<job-uuid>")["job"]
        print(job["title"], job.get("location"))
    except JobBoxApiError as err:
        print(err.status, err.code, err)
        raise
    ```

=== "PHP"

    ```php
    try {
        $job = $jobbox->jobs->get('<job-uuid>')['job'];
        echo $job['title'] . "\n";
    } catch (JobBoxApiException $err) {
        echo $err->status . ' ' . ($err->apiCode ?? '') . ' ' . $err->getMessage() . "\n";
        throw $err;
    }
    ```

---

## 4. What you can do next

| Goal | Guide |
|------|--------|
| Filter by salary, category, dates, benefits, … | [Search and filters](../guides/search-and-filters.md) |
| Page through large result sets | [Pagination](../guides/pagination.md) |
| Related roles | [Similar jobs](../guides/similar-jobs.md) |
| Category chips / country options | [Metadata](../guides/metadata.md) |
| Wire a Vue / React / Angular / Next.js board | [Examples](../examples/overview.md) |
| Wire a CodeIgniter / Laravel board | [Examples](../examples/overview.md) |
