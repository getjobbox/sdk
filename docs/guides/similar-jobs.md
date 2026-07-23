# Similar jobs

`jobs.similar(id)` returns related listings for a given job via `GET /api/v1/sdk/jobs/:id/similar`.

Use this for “More like this” panels on a detail page.

---

## Basic usage

=== "Node"

    ```ts
    const jobId = '11111111-2222-3333-4444-555555555555';
    const { jobs } = await jobbox.jobs.similar(jobId);

    for (const related of jobs) {
      console.log(related.id, related.title, related.company);
    }
    ```

=== "Python"

    ```python
    job_id = "11111111-2222-3333-4444-555555555555"
    related = jobbox.jobs.similar(job_id)["jobs"]

    for job in related:
        print(job["id"], job.get("title"), job.get("company"))
    ```

=== "PHP"

    ```php
    $jobId = '11111111-2222-3333-4444-555555555555';
    $related = $jobbox->jobs->similar($jobId)['jobs'];

    foreach ($related as $job) {
        echo $job['id'] . ' ' . ($job['title'] ?? '') . "\n";
    }
    ```

---

## Detail page pattern

Load the primary job and similar roles in parallel (or sequentially):

=== "Node"

    ```ts
    async function loadDetail(id: string) {
      const [{ job }, { jobs: similar }] = await Promise.all([
        jobbox.jobs.get(id),
        jobbox.jobs.similar(id),
      ]);
      return { job, similar };
    }
    ```

=== "Python"

    ```python
    def load_detail(job_id: str):
        job = jobbox.jobs.get(job_id)["job"]
        similar = jobbox.jobs.similar(job_id)["jobs"]
        return {"job": job, "similar": similar}
    ```

=== "PHP"

    ```php
    function loadDetail(JobBox $jobbox, string $id): array
    {
        return [
            'job' => $jobbox->jobs->get($id)['job'],
            'similar' => $jobbox->jobs->similar($id)['jobs'],
        ];
    }
    ```

---

## Empty results

If the API has no similar listings, `jobs` is an empty array - not an error.

=== "Node"

    ```ts
    const { jobs } = await jobbox.jobs.similar(jobId);
    if (jobs.length === 0) {
      console.log('No similar jobs');
    }
    ```

=== "Python"

    ```python
    jobs = jobbox.jobs.similar(job_id)["jobs"]
    if not jobs:
        print("No similar jobs")
    ```

=== "PHP"

    ```php
    $jobs = $jobbox->jobs->similar($jobId)['jobs'];
    if ($jobs === []) {
        echo "No similar jobs\n";
    }
    ```
