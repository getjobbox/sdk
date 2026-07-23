# Errors reference

---

## JobBoxApiError / JobBoxApiException

Raised for HTTP error responses from the JobBox API.

=== "Node"

    ```ts
    class JobBoxApiError extends Error {
      readonly status: number;
      readonly code: string | null;
      readonly requestId: string | null;
      readonly body: unknown;
      // message from Error
    }
    ```

=== "Python"

    ```python
    class JobBoxApiError(Exception):
        status: int
        code: str | None
        request_id: str | None
        body: Any
    ```

=== "PHP"

    ```php
    class JobBoxApiException extends \Exception {
        public int $status;
        public ?string $apiCode;
        public ?string $requestId;
        public mixed $body;
    }
    ```

### Typical status codes

| Status | Meaning |
|--------|---------|
| `401` / `403` | Missing, invalid, or revoked API key |
| `404` | Job (or resource) not found |
| `429` | Rate limited (GETs are retried automatically) |
| `5xx` | Upstream error (GETs are retried automatically) |

---

## JobBoxNetworkError / JobBoxNetworkException

Raised for transport failures, DNS errors, and timeouts (not an HTTP error body from JobBox).

=== "Node"

    ```ts
    class JobBoxNetworkError extends Error {
      readonly cause?: unknown;
    }
    ```

=== "Python"

    ```python
    class JobBoxNetworkError(Exception):
        cause_error: BaseException | None
    ```

=== "PHP"

    ```php
    class JobBoxNetworkException extends \Exception {
        public mixed $causeError;
    }
    ```

---

## Retry policy

| Rule | Detail |
|------|--------|
| Methods | Idempotent **GET** only |
| Statuses | `429`, `5xx` |
| Attempts | `1 + maxRetries` (default maxRetries = 2) |
| Backoff | Honor `Retry-After`; else exponential with jitter |

Guide: [Error handling](../guides/error-handling.md).
