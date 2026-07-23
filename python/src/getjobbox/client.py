from __future__ import annotations

from typing import Mapping

from getjobbox.http import HttpClient, Transport, build_user_agent, default_transport
from getjobbox.resources.jobs import JobsResource


class JobBox:
    """Official JobBox Python SDK client (Jobs resource)."""

    def __init__(
        self,
        *,
        api_key: str,
        base_url: str = "https://api.getjobbox.com",
        timeout: float = 30.0,
        max_retries: int = 2,
        app_name: str | None = None,
        default_headers: Mapping[str, str] | None = None,
        transport: Transport | None = None,
    ) -> None:
        if not api_key or not str(api_key).strip():
            raise ValueError("api_key is required")

        self._http = HttpClient(
            api_key=str(api_key).strip(),
            base_url=base_url,
            timeout=timeout,
            max_retries=max_retries,
            user_agent=build_user_agent(app_name),
            transport=transport or default_transport,
            default_headers=dict(default_headers or {}),
        )
        self.jobs = JobsResource(self._http)
