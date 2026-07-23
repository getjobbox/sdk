from __future__ import annotations

from typing import Any, Literal, TypedDict


class Job(TypedDict, total=False):
    """Wire-level job object (fields vary; typed loosely for forward compatibility)."""

    id: str
    title: str | None
    company: str | None
    location: str | None
    category: str | None
    work_mode: str | None
    seniority_level: str | None
    employment_type: str | None
    status: str | None


class JobListResult(TypedDict):
    jobs: list[dict[str, Any]]
    total: int
    page: int
    per_page: int


class JobGetResult(TypedDict):
    job: dict[str, Any]


class SimilarJobsResult(TypedDict):
    jobs: list[dict[str, Any]]


class CategoryItem(TypedDict, total=False):
    id: str
    slug: str
    label: str
    sort_order: int


class CategoriesResult(TypedDict):
    categories: list[CategoryItem]


class CountryOptionsResult(TypedDict, total=False):
    options: Any


class OpportunitiesCountResult(TypedDict, total=False):
    total: int


CompensationType = Literal["job", "gig"]
