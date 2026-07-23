# JobBox SDKs

Official partner SDKs for the JobBox Integration/SDK API.

**Canonical rules:** [STANDARD.md](./STANDARD.md) - all languages must follow it.

## Languages

| Language | Path | Package | Status |
|----------|------|---------|--------|
| Node.js (TypeScript) | [`node/`](./node) | `@getjobbox/sdk` | Stable (v0.1) |
| PHP | [`php/`](./php) | `getjobbox/sdk` | Planned |
| Python | [`python/`](./python) | `getjobbox` / `getjobbox-sdk` | Planned |

## Quick facts

- **Default base URL:** `https://api.getjobbox.com`
- **Auth header:** `X-JobBox-Api-Key`
- **Jobs API:** `GET /api/v1/sdk/jobs` (and related meta routes)
- **Env var for keys:** `JOBBOX_API_KEY`

Create keys with a JobBox session JWT via `POST /api/v1/api-keys`. Never commit plaintext keys.
