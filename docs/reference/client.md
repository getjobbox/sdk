# Client reference

Entry type: **`JobBox`**. Construct once; call resource methods on `jobs`.

---

## Constructor

=== "Node"

    ```ts
    import { JobBox } from '@getjobbox/sdk';

    const jobbox = new JobBox(options: JobBoxClientOptions);
    ```

    | Option | Type | Default | Required |
    |--------|------|---------|----------|
    | `apiKey` | `string` | — | yes |
    | `baseUrl` | `string` | `https://api.getjobbox.com` | no |
    | `timeoutMs` | `number` | `30000` | no |
    | `maxRetries` | `number` | `2` | no |
    | `appName` | `string` | — | no |
    | `fetch` | `typeof fetch` | `globalThis.fetch` | no |
    | `defaultHeaders` | `Record<string, string>` | `{}` | no |

=== "Python"

    ```python
    from getjobbox import JobBox

    jobbox = JobBox(**options)
    ```

    | Option | Type | Default | Required |
    |--------|------|---------|----------|
    | `api_key` | `str` | — | yes |
    | `base_url` | `str` | `https://api.getjobbox.com` | no |
    | `timeout` | `float` | `30.0` | no |
    | `max_retries` | `int` | `2` | no |
    | `app_name` | `str` | — | no |
    | `transport` | callable | urllib | no |
    | `default_headers` | `dict` | `{}` | no |

=== "PHP"

    ```php
    use GetJobBox\JobBox;

    $jobbox = new JobBox($options);
    ```

    | Option | Type | Default | Required |
    |--------|------|---------|----------|
    | `apiKey` | `string` | — | yes |
    | `baseUrl` | `string` | `https://api.getjobbox.com` | no |
    | `timeout` | `float` | `30.0` | no |
    | `maxRetries` | `int` | `2` | no |
    | `appName` | `string` | — | no |
    | `transport` | transport / callable | cURL | no |
    | `defaultHeaders` | `array` | `[]` | no |

---

## Resources

| Property | Type | Description |
|----------|------|-------------|
| `jobs` | Jobs resource | List, get, similar, meta |

Future HTTP modules become new resources (`applications`, …), not flat methods on `JobBox`.

---

## Exports

=== "Node"

    - Classes: `JobBox`, `JobBoxApiError`, `JobBoxNetworkError`
    - Const: `VERSION`
    - Types: `JobBoxClientOptions`, `Job`, `JobListParams`, `JobListResult`, …

=== "Python"

    - `JobBox`, `JobBoxApiError`, `JobBoxNetworkError`, `VERSION`

=== "PHP"

    - `GetJobBox\JobBox`
    - `GetJobBox\Exceptions\JobBoxApiException`
    - `GetJobBox\Exceptions\JobBoxNetworkException`

---

## HTTP details

| Item | Value |
|------|--------|
| API prefix | `/api/v1` |
| Auth header | `X-JobBox-Api-Key` |
| Success envelope | Unwrapped to `data` |
| Wire naming | snake_case |
| User-Agent | `JobBox{Lang}SDK/<semver>` |

See [STANDARD.md](https://github.com/getjobbox/sdk/blob/main/STANDARD.md) for the full cross-language contract.
