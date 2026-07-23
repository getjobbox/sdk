# Get a job

`jobs.get(id)` loads a single listing by UUID via `GET /api/v1/sdk/jobs/:id`.

---

## Fetch by ID

=== "Node"

    ```ts
    const { job } = await jobbox.jobs.get('11111111-2222-3333-4444-555555555555');

    console.log(job.title);
    console.log(job.company, job.location);
    console.log(job.work_mode, job.seniority_level);
    ```

=== "Python"

    ```python
    job = jobbox.jobs.get("11111111-2222-3333-4444-555555555555")["job"]

    print(job["title"])
    print(job.get("company"), job.get("location"))
    print(job.get("work_mode"), job.get("seniority_level"))
    ```

=== "PHP"

    ```php
    $job = $jobbox->jobs->get('11111111-2222-3333-4444-555555555555')['job'];

    echo $job['title'] . "\n";
    echo ($job['company'] ?? '') . ' ' . ($job['location'] ?? '') . "\n";
    ```

---

## From list → detail

Typical board flow: list cards, then open detail with the job’s `id`.

=== "Node"

    ```ts
    const { jobs } = await jobbox.jobs.list({ search: 'nurse', page: 1 });
    const firstId = jobs[0]?.id;
    if (!firstId) throw new Error('No jobs found');

    const { job } = await jobbox.jobs.get(firstId);
    console.log(job);
    ```

=== "Python"

    ```python
    result = jobbox.jobs.list(search="nurse", page=1)
    first_id = result["jobs"][0]["id"] if result["jobs"] else None
    if not first_id:
        raise RuntimeError("No jobs found")

    job = jobbox.jobs.get(first_id)["job"]
    print(job)
    ```

=== "PHP"

    ```php
    $result = $jobbox->jobs->list(['search' => 'nurse', 'page' => 1]);
    if ($result['jobs'] === []) {
        throw new RuntimeException('No jobs found');
    }
    $firstId = $result['jobs'][0]['id'];
    $job = $jobbox->jobs->get($firstId)['job'];
    ```

---

## Not found and other errors

Missing IDs typically return HTTP `404`, mapped to the language’s API error type:

=== "Node"

    ```ts
    import { JobBoxApiError } from '@getjobbox/sdk';

    try {
      await jobbox.jobs.get('00000000-0000-0000-0000-000000000000');
    } catch (err) {
      if (err instanceof JobBoxApiError && err.status === 404) {
        console.log('Job not found');
        return;
      }
      throw err;
    }
    ```

=== "Python"

    ```python
    from getjobbox import JobBoxApiError

    try:
        jobbox.jobs.get("00000000-0000-0000-0000-000000000000")
    except JobBoxApiError as err:
        if err.status == 404:
            print("Job not found")
        else:
            raise
    ```

=== "PHP"

    ```php
    use GetJobBox\Exceptions\JobBoxApiException;

    try {
        $jobbox->jobs->get('00000000-0000-0000-0000-000000000000');
    } catch (JobBoxApiException $err) {
        if ($err->status === 404) {
            echo "Job not found\n";
            return;
        }
        throw $err;
    }
    ```

See [Error handling](error-handling.md) for fields like `code` and `requestId`.

---

## Related

- [Similar jobs](similar-jobs.md)  
- [List jobs](list-jobs.md)
