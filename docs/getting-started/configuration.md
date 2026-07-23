# Configuration

Construct a `JobBox` client once (typically at process startup) and reuse it. All Jobs methods live under `client.jobs`.

---

## Client options

=== "Node"

    | Option | Default | Notes |
    |--------|---------|--------|
    | `apiKey` | **required** | Partner key |
    | `baseUrl` | `https://api.getjobbox.com` | No trailing slash needed |
    | `timeoutMs` | `30000` | Request timeout in milliseconds |
    | `maxRetries` | `2` | Extra GET attempts on `429` / `5xx` |
    | `appName` | - | Appended to `User-Agent` |
    | `fetch` | `globalThis.fetch` | Injectable for tests |
    | `defaultHeaders` | `{}` | Extra headers on every request |

    ```ts
    import { JobBox } from '@getjobbox/sdk';

    const jobbox = new JobBox({
      apiKey: process.env.JOBBOX_API_KEY!,
      baseUrl: process.env.JOBBOX_BASE_URL || 'https://api.getjobbox.com',
      timeoutMs: 30_000,
      maxRetries: 2,
      appName: 'my-job-board',
    });
    ```

=== "Python"

    | Option | Default | Notes |
    |--------|---------|--------|
    | `api_key` | **required** | Partner key |
    | `base_url` | `https://api.getjobbox.com` | No trailing slash needed |
    | `timeout` | `30.0` | Seconds |
    | `max_retries` | `2` | Extra GET attempts on `429` / `5xx` |
    | `app_name` | - | Appended to `User-Agent` |
    | `transport` | stdlib `urllib` | Injectable for tests |
    | `default_headers` | `{}` | Extra headers |

    ```python
    import os
    from getjobbox import JobBox

    jobbox = JobBox(
        api_key=os.environ["JOBBOX_API_KEY"],
        base_url=os.environ.get("JOBBOX_BASE_URL", "https://api.getjobbox.com"),
        timeout=30.0,
        max_retries=2,
        app_name="my-job-board",
    )
    ```

=== "PHP"

    | Option | Default | Notes |
    |--------|---------|--------|
    | `apiKey` | **required** | Partner key |
    | `baseUrl` | `https://api.getjobbox.com` | No trailing slash needed |
    | `timeout` | `30.0` | Seconds |
    | `maxRetries` | `2` | Extra GET attempts on `429` / `5xx` |
    | `appName` | - | Appended to `User-Agent` |
    | `transport` | cURL | Injectable `TransportInterface` or callable |
    | `defaultHeaders` | `[]` | Extra headers |

    ```php
    <?php
    use GetJobBox\JobBox;

    $jobbox = new JobBox([
        'apiKey' => getenv('JOBBOX_API_KEY') ?: '',
        'baseUrl' => getenv('JOBBOX_BASE_URL') ?: 'https://api.getjobbox.com',
        'timeout' => 30.0,
        'maxRetries' => 2,
        'appName' => 'my-job-board',
    ]);
    ```

---

## Base URL

Default production host is `https://api.getjobbox.com`. Paths are always under `/api/v1` (the SDK prepends this). You normally only override `baseUrl` for staging or local API proxies.

---

## Retries and timeouts

- Only **idempotent GET** requests are retried.
- Retries happen on HTTP **`429`** and **`5xx`**.
- When the response includes `Retry-After`, the client honors it; otherwise it uses exponential backoff with jitter.
- Timeouts abort a single attempt; exhausted retries surface as a network / timeout error. See [Error handling](../guides/error-handling.md).

---

## User-Agent

Examples:

- `JobBoxNodeSDK/0.1.0`
- `JobBoxPythonSDK/0.1.0`
- `JobBoxPhpSDK/0.1.0`

With `appName` / `app_name` set to `my-job-board`, the UA includes that suffix for easier support diagnostics.

---

## Naming conventions

| Layer | Convention |
|-------|------------|
| HTTP wire (query + JSON) | **snake_case** |
| Node / PHP list params | **camelCase** (`workMode`, `perPage`) |
| Python list kwargs | **snake_case** (`work_mode`, `per_page`) |

Multi-value filters accept arrays / lists in-language; the SDK serializes them to **CSV** on the wire.

---

## Next steps

- [Quickstart](quickstart.md)  
- [Client reference](../reference/client.md)
