"""Official JobBox Python SDK."""

from getjobbox._version import VERSION
from getjobbox.client import JobBox
from getjobbox.errors import JobBoxApiError, JobBoxNetworkError

__all__ = [
    "VERSION",
    "JobBox",
    "JobBoxApiError",
    "JobBoxNetworkError",
]
