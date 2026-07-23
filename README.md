# JobBox SDKs

Official partner SDKs for the JobBox Integration/SDK API.

**Canonical rules:** [STANDARD.md](./STANDARD.md) - all languages must follow it.

## Languages

| Language | Path | Package | Status | Registry |
|----------|------|---------|--------|----------|
| Node.js (TypeScript) | [`node/`](./node) | `@getjobbox/sdk` | Stable (v0.1) | GitHub Packages |
| PHP | [`php/`](./php) | `getjobbox/sdk` | Stable (v0.1) | GitHub Releases |
| Python | [`python/`](./python) | `getjobbox` | Stable (v0.1) | GitHub Releases |

## Examples

| Example | Path | Stack |
|---------|------|-------|
| Vue.js job board | [`examples/vue/`](./examples/vue) | Vue 3 + Vite + Node or Python SDK proxy |
| React job board | [`examples/react/`](./examples/react) | React + Vite + Node or Python SDK proxy |
| Angular job board | [`examples/angular/`](./examples/angular) | Angular 19 + CLI + Node or Python SDK proxy |

## Quick facts

- **Default base URL:** `https://api.getjobbox.com`
- **Auth header:** `X-JobBox-Api-Key`
- **Jobs API:** `GET /api/v1/sdk/jobs` (and related meta routes)
- **Env var for keys:** `JOBBOX_API_KEY`

Create keys with a JobBox session JWT via `POST /api/v1/api-keys`. Never commit plaintext keys.

## Releasing

Languages version and publish independently. Bump the version in that language’s metadata + `CHANGELOG.md`, commit, then tag and push:

| Language | Version file | Tag |
|----------|--------------|-----|
| Node | `node/package.json` | `node-vX.Y.Z` |
| Python | `python/pyproject.toml` | `python-vX.Y.Z` |
| PHP | `php/src/Version.php` | `php-vX.Y.Z` |

```bash
git tag node-v0.1.0
git push origin node-v0.1.0
```

The [publish workflow](./.github/workflows/publish.yml) verifies the tag matches package metadata, runs tests, then:

- **Node** → publishes to GitHub Packages
- **Python / PHP** → creates a GitHub Release with installable assets
