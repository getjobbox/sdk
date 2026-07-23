# JobBox SDK

Official partner SDKs for the [JobBox](https://getjobbox.com) Integration / SDK API. Use them to list, search, and fetch jobs from the JobBox catalog with a keyed API - from Node.js, Python, or PHP.

## Languages

| Language | Package | Status | Registry |
|----------|---------|--------|----------|
| Node.js (TypeScript) | `@getjobbox/sdk` | Stable (v0.1) | [GitHub Packages](getting-started/installation.md#nodejs-typescript) |
| Python | `getjobbox` | Stable (v0.1) | [GitHub Releases](getting-started/installation.md#python) |
| PHP | `getjobbox/sdk` | Stable (v0.1) | [GitHub Releases](getting-started/installation.md#php) |

## Quick facts

- **Default base URL:** `https://api.getjobbox.com`
- **Auth header:** `X-JobBox-Api-Key`
- **Jobs API:** `GET /api/v1/sdk/jobs` (and related meta routes)
- **Env var for keys:** `JOBBOX_API_KEY`

!!! warning "Never expose your API key"
    Keep `JOBBOX_API_KEY` on the server. Do not put it in browser bundles, mobile apps, or public repos. See [Server-side pattern](guides/server-side-pattern.md).

## Path through the docs

1. [Install](getting-started/installation.md) the SDK for your language
2. [Authenticate](getting-started/authentication.md) with an API key
3. [Configure](getting-started/configuration.md) the client
4. Run the [quickstart](getting-started/quickstart.md)
5. Dig into [search and filters](guides/search-and-filters.md), [pagination](guides/pagination.md), and the rest of the guides

## Example apps

Ready-made job boards that call the SDK from a local proxy:

**JS / TS:** [Vue](examples/vue.md) · [React](examples/react.md) · [Angular](examples/angular.md) · [Next.js](examples/next.md)

**PHP:** [CodeIgniter](examples/codeigniter.md) · [Laravel](examples/laravel.md)

See the [examples overview](examples/overview.md) for ports and quick starts.

## Canonical contract

Cross-language rules (HTTP surface, client shape, retries, packaging) live in [`STANDARD.md`](https://github.com/getjobbox/sdk/blob/main/STANDARD.md) in the repository.
