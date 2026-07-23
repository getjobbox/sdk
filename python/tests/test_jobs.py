from __future__ import annotations

import json
import re
from typing import Any, Mapping
from urllib.parse import parse_qs, urlparse

import pytest

from getjobbox import JobBox, JobBoxApiError


def _json_response(
    status: int,
    body: Any,
    headers: Mapping[str, str] | None = None,
) -> tuple[int, dict[str, str], bytes]:
    resp_headers = {"Content-Type": "application/json", **(headers or {})}
    return status, resp_headers, json.dumps(body).encode("utf-8")


def test_sends_api_key_and_user_agent() -> None:
    calls: list[tuple[str, str, Mapping[str, str]]] = []

    def transport(
        method: str,
        url: str,
        headers: Mapping[str, str],
        body: bytes | None,
        timeout: float,
    ) -> tuple[int, dict[str, str], bytes]:
        calls.append((method, url, headers))
        assert headers.get("X-JobBox-Api-Key") == "jb_test_secret"
        assert re.match(r"^JobBoxPythonSDK/", headers.get("User-Agent", ""))
        assert url == "https://api.getjobbox.com/api/v1/sdk/jobs?page=1&per_page=28"
        return _json_response(200, {"success": True, "data": {"jobs": [], "total": 0}})

    client = JobBox(api_key="jb_test_secret", transport=transport)
    client.jobs.list()
    assert len(calls) == 1


def test_serializes_array_filters_as_csv_snake_case() -> None:
    def transport(
        method: str,
        url: str,
        headers: Mapping[str, str],
        body: bytes | None,
        timeout: float,
    ) -> tuple[int, dict[str, str], bytes]:
        parsed = urlparse(url)
        params = parse_qs(parsed.query)
        assert params.get("work_mode") == ["remote,hybrid"]
        assert params.get("seniority_level") == ["senior"]
        assert params.get("search") == ["react"]
        return _json_response(
            200,
            {
                "success": True,
                "data": {"jobs": [{"id": "1", "title": "Engineer"}], "total": 1},
            },
        )

    client = JobBox(api_key="jb_test_secret", transport=transport)
    result = client.jobs.list(
        search="react",
        work_mode=["remote", "hybrid"],
        seniority_level=["senior"],
    )
    assert result["total"] == 1
    assert result["jobs"][0]["title"] == "Engineer"
    assert result["page"] == 1
    assert result["per_page"] == 28


def test_unwraps_data_envelope_for_get() -> None:
    def transport(
        method: str,
        url: str,
        headers: Mapping[str, str],
        body: bytes | None,
        timeout: float,
    ) -> tuple[int, dict[str, str], bytes]:
        return _json_response(
            200,
            {"success": True, "data": {"job": {"id": "abc", "title": "Dev"}}},
        )

    client = JobBox(api_key="k", transport=transport)
    job = client.jobs.get("abc")["job"]
    assert job["title"] == "Dev"


def test_maps_api_errors_to_jobbox_api_error() -> None:
    def transport(
        method: str,
        url: str,
        headers: Mapping[str, str],
        body: bytes | None,
        timeout: float,
    ) -> tuple[int, dict[str, str], bytes]:
        return _json_response(
            401,
            {
                "success": False,
                "code": "JB_APIKEY_401",
                "message": "Invalid or revoked API key",
            },
        )

    client = JobBox(api_key="bad", transport=transport, max_retries=0)
    with pytest.raises(JobBoxApiError) as exc_info:
        client.jobs.list()
    err = exc_info.value
    assert err.status == 401
    assert err.code == "JB_APIKEY_401"


def test_retries_get_on_503_then_succeeds() -> None:
    calls = {"n": 0}

    def transport(
        method: str,
        url: str,
        headers: Mapping[str, str],
        body: bytes | None,
        timeout: float,
    ) -> tuple[int, dict[str, str], bytes]:
        calls["n"] += 1
        if calls["n"] == 1:
            return _json_response(503, {"success": False, "message": "unavailable"})
        return _json_response(200, {"success": True, "data": {"categories": []}})

    client = JobBox(api_key="k", transport=transport, max_retries=2)
    result = client.jobs.categories()
    assert result["categories"] == []
    assert calls["n"] == 2


def test_requires_api_key() -> None:
    with pytest.raises(ValueError, match="api_key"):
        JobBox(api_key="")
