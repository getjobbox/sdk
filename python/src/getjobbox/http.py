from __future__ import annotations

import json
import random
import time
import urllib.error
import urllib.parse
import urllib.request
from typing import Any, Callable, Mapping, MutableMapping

from getjobbox._version import VERSION
from getjobbox.errors import JobBoxApiError, JobBoxNetworkError

HttpMethod = str  # "GET" | "POST" | "PUT" | "PATCH" | "DELETE"

TransportResponse = tuple[int, Mapping[str, str], bytes]
Transport = Callable[[str, str, Mapping[str, str], bytes | None, float], TransportResponse]


def to_csv(value: str | list[str] | None) -> str | None:
    if value is None:
        return None
    if isinstance(value, list):
        parts = [str(v).strip() for v in value if str(v).strip()]
        return ",".join(parts) if parts else None
    trimmed = str(value).strip()
    return trimmed or None


def build_query(params: Mapping[str, Any]) -> str:
    items: list[tuple[str, str]] = []
    for key, value in params.items():
        if value is None or value == "":
            continue
        items.append((key, str(value)))
    if not items:
        return ""
    return "?" + urllib.parse.urlencode(items)


def build_user_agent(app_name: str | None = None) -> str:
    base = f"JobBoxPythonSDK/{VERSION}"
    if app_name and str(app_name).strip():
        return f"{base} {str(app_name).strip()}"
    return base


def _parse_retry_after_ms(header: str | None) -> float | None:
    if not header:
        return None
    try:
        as_int = int(header)
        if as_int >= 0:
            return as_int * 1000.0
    except ValueError:
        pass
    try:
        # HTTP-date form
        from email.utils import parsedate_to_datetime

        dt = parsedate_to_datetime(header)
        return max(0.0, (dt.timestamp() - time.time()) * 1000.0)
    except (TypeError, ValueError, OverflowError):
        return None


def _backoff_ms(attempt: int, retry_after_ms: float | None) -> float:
    if retry_after_ms is not None:
        return retry_after_ms
    base = min(8000.0, 250.0 * (2**attempt))
    jitter = random.random() * 100.0
    return base + jitter


def _should_retry(method: str, status: int) -> bool:
    if method != "GET":
        return False
    return status == 429 or status >= 500


def _header_get(headers: Mapping[str, str], name: str) -> str | None:
    lower = name.lower()
    for key, value in headers.items():
        if key.lower() == lower:
            return value
    return None


def _default_transport(
    method: str,
    url: str,
    headers: Mapping[str, str],
    body: bytes | None,
    timeout: float,
) -> TransportResponse:
    req = urllib.request.Request(url, data=body, method=method)
    for key, value in headers.items():
        req.add_header(key, value)
    try:
        with urllib.request.urlopen(req, timeout=timeout) as resp:
            status = getattr(resp, "status", None) or resp.getcode()
            resp_headers = {k: v for k, v in resp.headers.items()}
            data = resp.read()
            return int(status), resp_headers, data
    except urllib.error.HTTPError as exc:
        status = exc.code
        resp_headers = {k: v for k, v in (exc.headers.items() if exc.headers else [])}
        data = exc.read() if hasattr(exc, "read") else b""
        return int(status), resp_headers, data


class HttpClient:
    def __init__(
        self,
        *,
        api_key: str,
        base_url: str,
        timeout: float,
        max_retries: int,
        user_agent: str,
        transport: Transport,
        default_headers: Mapping[str, str],
    ) -> None:
        self._api_key = api_key
        self._base_url = base_url.rstrip("/")
        self._timeout = timeout
        self._max_retries = max_retries
        self._user_agent = user_agent
        self._transport = transport
        self._default_headers = dict(default_headers)

    def request(
        self,
        method: str,
        path: str,
        *,
        query: Mapping[str, Any] | None = None,
        body: Any = None,
    ) -> Any:
        url = f"{self._base_url}/api/v1{path}{build_query(query or {})}"
        body_bytes: bytes | None = None
        last_error: Any = None

        for attempt in range(self._max_retries + 1):
            headers: MutableMapping[str, str] = {
                "Accept": "application/json",
                "X-JobBox-Api-Key": self._api_key,
                "User-Agent": self._user_agent,
                **self._default_headers,
            }
            if body is not None:
                headers["Content-Type"] = "application/json"
                body_bytes = json.dumps(body).encode("utf-8")

            try:
                status, resp_headers, raw = self._transport(
                    method, url, headers, body_bytes, self._timeout
                )

                request_id = (
                    _header_get(resp_headers, "x-request-id")
                    or _header_get(resp_headers, "x-jobbox-request-id")
                )

                parsed: Any = None
                text = raw.decode("utf-8") if raw else ""
                if text:
                    try:
                        parsed = json.loads(text)
                    except json.JSONDecodeError:
                        parsed = text

                if not (200 <= status < 300):
                    if attempt < self._max_retries and _should_retry(method, status):
                        wait_ms = _backoff_ms(
                            attempt, _parse_retry_after_ms(_header_get(resp_headers, "retry-after"))
                        )
                        time.sleep(wait_ms / 1000.0)
                        continue

                    err_body = parsed if isinstance(parsed, dict) else None
                    message = (
                        err_body.get("message")
                        if err_body and isinstance(err_body.get("message"), str)
                        else f"JobBox API request failed with status {status}"
                    )
                    code = (
                        err_body.get("code")
                        if err_body and isinstance(err_body.get("code"), str)
                        else None
                    )
                    raise JobBoxApiError(
                        message,
                        status=status,
                        code=code,
                        request_id=request_id,
                        body=parsed,
                    )

                if isinstance(parsed, dict) and "data" in parsed:
                    return parsed["data"]
                return parsed

            except JobBoxApiError:
                raise
            except Exception as error:  # noqa: BLE001 - mirror Node catch-all for transport
                last_error = error
                is_timeout = isinstance(error, TimeoutError) or (
                    isinstance(error, OSError) and "timed out" in str(error).lower()
                )
                if attempt < self._max_retries and method == "GET" and is_timeout:
                    time.sleep(_backoff_ms(attempt, None) / 1000.0)
                    continue
                if is_timeout:
                    raise JobBoxNetworkError("JobBox API request timed out", error) from error
                if attempt < self._max_retries and method == "GET":
                    time.sleep(_backoff_ms(attempt, None) / 1000.0)
                    continue
                message = str(error) if isinstance(error, Exception) else "JobBox API network error"
                raise JobBoxNetworkError(message, error) from error

        raise JobBoxNetworkError("JobBox API request failed after retries", last_error)


default_transport = _default_transport
