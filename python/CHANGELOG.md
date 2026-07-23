# Changelog

## 0.1.0

- Initial Jobs resource: `list`, `get`, `similar`, `categories`, `country_options`, `opportunities_count`
- Auth via `X-JobBox-Api-Key`
- Retries on 429/5xx for GET, typed `JobBoxApiError` / `JobBoxNetworkError`
