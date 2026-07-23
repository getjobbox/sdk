from __future__ import annotations

from typing import Any


class JobBoxApiError(Exception):
    """HTTP API error response from JobBox."""

    def __init__(
        self,
        message: str,
        *,
        status: int,
        code: str | None = None,
        request_id: str | None = None,
        body: Any = None,
    ) -> None:
        super().__init__(message)
        self.status = status
        self.code = code
        self.request_id = request_id
        self.body = body


class JobBoxNetworkError(Exception):
    """Transport / timeout failure talking to JobBox."""

    def __init__(self, message: str, cause: Any = None) -> None:
        super().__init__(message)
        self.cause_error = cause
