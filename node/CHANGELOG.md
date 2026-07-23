# Changelog

## 0.1.0

- Initial Jobs resource: `list`, `get`, `similar`, `categories`, `countryOptions`, `opportunitiesCount`
- Auth via `X-JobBox-Api-Key`
- Retries on 429/5xx for GET, typed `JobBoxApiError` / `JobBoxNetworkError`
