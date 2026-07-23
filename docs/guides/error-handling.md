# Error handling

SDKs distinguish **API errors** (HTTP non-2xx with a JobBox body) from **network / timeout** failures.

| Language | API error | Network error |
|----------|-----------|---------------|
| Node | `JobBoxApiError` | `JobBoxNetworkError` |
| Python | `JobBoxApiError` | `JobBoxNetworkError` |
| PHP | `JobBoxApiException` | `JobBoxNetworkException` |

---

## API errors

Thrown when the JobBox API returns an error response. Common fields:

| Field | Node | Python | PHP |
|-------|------|--------|-----|
| HTTP status | `status` | `status` | `status` |
| API code | `code` | `code` | `apiCode` |
| Message | `message` | message / `str(err)` | `getMessage()` |
| Request id | `requestId` | `request_id` | `requestId` |
| Raw body | `body` | `body` | `body` |

=== "Node"

    ```ts
    import { JobBox, JobBoxApiError, JobBoxNetworkError } from '@getjobbox/sdk';

    try {
      const { job } = await jobbox.jobs.get(id);
      return job;
    } catch (err) {
      if (err instanceof JobBoxApiError) {
        console.error('API error', {
          status: err.status,
          code: err.code,
          message: err.message,
          requestId: err.requestId,
        });
        if (err.status === 401 || err.status === 403) {
          // Invalid or revoked API key
        }
        if (err.status === 404) {
          // Unknown job id
        }
        if (err.status === 429) {
          // Rate limited (SDK already retried GETs)
        }
        throw err;
      }
      if (err instanceof JobBoxNetworkError) {
        console.error('Network / timeout', err.message);
        throw err;
      }
      throw err;
    }
    ```

=== "Python"

    ```python
    from getjobbox import JobBoxApiError, JobBoxNetworkError

    try:
        job = jobbox.jobs.get(job_id)["job"]
    except JobBoxApiError as err:
        print(err.status, err.code, err, err.request_id)
        if err.status in (401, 403):
            pass  # bad key
        if err.status == 404:
            pass  # missing job
        raise
    except JobBoxNetworkError as err:
        print("Network / timeout", err)
        raise
    ```

=== "PHP"

    ```php
    use GetJobBox\Exceptions\JobBoxApiException;
    use GetJobBox\Exceptions\JobBoxNetworkException;

    try {
        $job = $jobbox->jobs->get($id)['job'];
    } catch (JobBoxApiException $err) {
        error_log(sprintf(
            'API %s %s %s req=%s',
            $err->status,
            $err->apiCode ?? '',
            $err->getMessage(),
            $err->requestId ?? ''
        ));
        throw $err;
    } catch (JobBoxNetworkException $err) {
        error_log('Network / timeout: ' . $err->getMessage());
        throw $err;
    }
    ```

---

## Retries

For **GET** requests only:

- Status **`429`** and **`5xx`** are retried up to `maxRetries` / `max_retries` (default **2** extra attempts).
- `Retry-After` is honored when present; otherwise exponential backoff with jitter applies.
- If retries are exhausted, you still receive the final API or network error.

You do not need to implement retry logic yourself for standard list/get/meta calls.

---

## Invalid client config

Missing `apiKey` / `api_key` throws a normal language error at construction time (before any HTTP call), not `JobBoxApiError`.

=== "Node"

    ```ts
    // Throws: JobBox SDK requires an apiKey …
    new JobBox({ apiKey: '' });
    ```

=== "Python"

    ```python
    # Raises if api_key is empty
    JobBox(api_key="")
    ```

=== "PHP"

    ```php
    // Throws if apiKey is empty
    new JobBox(['apiKey' => '']);
    ```

---

## Logging hygiene

Never log the full API key. If you log headers or config for debugging, redact to a short prefix only.

---

## Related

- [Errors reference](../reference/errors.md)  
- [Configuration](../getting-started/configuration.md)
