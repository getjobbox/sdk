from __future__ import annotations

from typing import Any
from urllib.parse import quote

from getjobbox.http import HttpClient, to_csv
from getjobbox.types import (
    CategoriesResult,
    CountryOptionsResult,
    JobGetResult,
    JobListResult,
    OpportunitiesCountResult,
    SimilarJobsResult,
)


def _opportunity_only_wire(value: bool | str | None) -> str | None:
    if value is None:
        return None
    if value is True or value == "true" or value == "1":
        return "true"
    if value is False or value == "false" or value == "0":
        return "false"
    return None


class JobsResource:
    def __init__(self, http: HttpClient) -> None:
        self._http = http

    def list(
        self,
        *,
        search: str | None = None,
        location: str | None = None,
        country: str | None = None,
        work_mode: str | list[str] | None = None,
        seniority_level: str | list[str] | None = None,
        employment_types: str | list[str] | None = None,
        benefit_filters: str | list[str] | None = None,
        companies: str | list[str] | None = None,
        date_from: str | None = None,
        date_to: str | None = None,
        salary_min: float | int | None = None,
        salary_max: float | int | None = None,
        category: str | None = None,
        compensation_type: str | None = None,
        application_mode: str | list[str] | None = None,
        opportunity_only: bool | str | None = None,
        page: int | None = None,
        per_page: int | None = None,
    ) -> JobListResult:
        page_val = 1 if page is None else page
        per_page_val = 28 if per_page is None else per_page

        data = self._http.request(
            "GET",
            "/sdk/jobs",
            query={
                "search": search,
                "location": location,
                "country": country,
                "work_mode": to_csv(work_mode),
                "seniority_level": to_csv(seniority_level),
                "employment_types": to_csv(employment_types),
                "benefit_filters": to_csv(benefit_filters),
                "companies": to_csv(companies),
                "date_from": date_from,
                "date_to": date_to,
                "salary_min": salary_min,
                "salary_max": salary_max,
                "category": category,
                "compensation_type": compensation_type,
                "application_mode": to_csv(application_mode),
                "opportunity_only": _opportunity_only_wire(opportunity_only),
                "page": page_val,
                "per_page": per_page_val,
            },
        )

        jobs: list[dict[str, Any]] = []
        total = 0
        if isinstance(data, dict):
            raw_jobs = data.get("jobs")
            if isinstance(raw_jobs, list):
                jobs = raw_jobs
            raw_total = data.get("total")
            if isinstance(raw_total, (int, float)):
                total = int(raw_total)

        return {
            "jobs": jobs,
            "total": total,
            "page": page_val,
            "per_page": per_page_val,
        }

    def get(self, job_id: str) -> JobGetResult:
        data = self._http.request("GET", f"/sdk/jobs/{quote(job_id, safe='')}")
        job = data.get("job") if isinstance(data, dict) else None
        return {"job": job if isinstance(job, dict) else {}}

    def similar(self, job_id: str) -> SimilarJobsResult:
        data = self._http.request("GET", f"/sdk/jobs/{quote(job_id, safe='')}/similar")
        if isinstance(data, list):
            return {"jobs": data}
        if isinstance(data, dict) and isinstance(data.get("jobs"), list):
            return {"jobs": data["jobs"]}
        return {"jobs": []}

    def categories(self) -> CategoriesResult:
        return self._http.request("GET", "/sdk/jobs/meta/categories")

    def country_options(self) -> CountryOptionsResult:
        return self._http.request("GET", "/sdk/jobs/meta/country-options")

    def opportunities_count(self) -> OpportunitiesCountResult:
        return self._http.request("GET", "/sdk/jobs/meta/opportunities-count")
