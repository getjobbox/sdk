# JobBox SDK Standard

**Version:** 1.0.0  
**Status:** Canonical  
**Applies to:** All partner SDKs under `sdk/<language>/`

This document is the source of truth for JobBox partner SDKs. Language packages (Node, PHP, Python, and any future runtime) **must** conform. When conventions or the HTTP surface change, update this file first, then update language packages.

Root [`README.md`](./README.md) is an index only - it does not redefine these rules.

---

## 1. Purpose and scope

### In scope

- Official JobBox **partner** SDKs that call the Integration/SDK HTTP API
- Public catalog Jobs access via the keyed SDK surface
- Shared auth, client shape, errors, retries, packaging, and docs expectations

### Out of scope

- Internal monorepo packages (`@jobbox/*` under `shared/`, `backend/packages/`)
- MCP server / tool contracts
- Frontend Axios clients and app stores
- Unauthenticated website routes (`GET /api/v1/jobs/public*`)

### Versioning

| Layer | Scheme |
|-------|--------|
| This standard | `MAJOR.MINOR.PATCH` in this file’s header |
| Language packages | Independent semver per package |

Breaking changes to the wire contract or required client shape bump the **standard major** and typically the affected package majors.

---

## 2. Repository layout

```
sdk/
  STANDARD.md              # this document
  README.md                # language status matrix + link here
  <language>/              # one directory per language runtime
    README.md
    CHANGELOG.md
    LICENSE
    …sources / build config…
```

### Rules

1. Allowed language directories include: `node`, `php`, `python` (and later `go`, `ruby`, etc.).
2. **No** language sources at `sdk/` root - only `STANDARD.md` and `README.md`.
3. Hard package boundary: SDKs **must not** import JobBox `backend/`, `frontend/`, `shared/`, `workers/`, or `apps-backend/` code.
4. Each language publishes independently.

---

## 3. HTTP contract (all languages)

| Item | Rule |
|------|------|
| Default base URL | `https://api.getjobbox.com` |
| API prefix | `/api/v1` |
| Jobs resource base | `/sdk/jobs` |
| Auth header | `X-JobBox-Api-Key: <plaintext_key>` (required on SDK routes) |
| JWT | Not used for the SDK Jobs surface |
| Content-Type | `application/json` when sending a body |
| Wire naming | **snake_case** for query params and JSON fields as returned by the API |
| Success envelope | `{ success, code?, message?, data }` - SDKs unwrap `data` on success |
| Error body | Prefer `{ success: false, code?, message?, … }` - map into typed SDK errors |

### Jobs endpoints (v1)

| HTTP | Path | SDK method |
|------|------|------------|
| GET | `/sdk/jobs` | `jobs.list(params)` |
| GET | `/sdk/jobs/:id` | `jobs.get(id)` |
| GET | `/sdk/jobs/:id/similar` | `jobs.similar(id)` |
| GET | `/sdk/jobs/meta/categories` | `jobs.categories()` |
| GET | `/sdk/jobs/meta/country-options` | `jobs.countryOptions()` |
| GET | `/sdk/jobs/meta/opportunities-count` | `jobs.opportunitiesCount()` |

### List filter params (wire names)

`search`, `location`, `country`, `work_mode`, `seniority_level`, `employment_types`, `benefit_filters`, `companies`, `date_from`, `date_to`, `salary_min`, `salary_max`, `category`, `compensation_type`, `application_mode`, `opportunity_only`, `page`, `per_page`

- Multi-value filters are **CSV** on the wire.
- Idiomatic language APIs may accept arrays / lists and **must** serialize them to CSV.

### API key management (not part of the Jobs resource)

Keys are created via authenticated REST (`POST /api/v1/api-keys` with JWT). SDKs consume keys; they do not need to implement key CRUD in v1.

---

## 4. Client shape (all languages)

1. Entry type named **`JobBox`** (or the closest idiomatic equivalent if the name conflicts).
2. Construct with config: required `apiKey`; optional `baseUrl`, `timeout` / `timeoutMs`, `maxRetries`, and a custom HTTP transport where the language supports it.
3. **Resource-oriented:** `client.jobs.*` - do not expose flat top-level RPCs for Jobs.
4. Future HTTP modules become new resources (`client.applications`, …), not methods dumped on the root client.
5. Public method names in-language should be idiomatic:
   - TypeScript/JavaScript: **camelCase** (`workMode`, `perPage`)
   - Python: **snake_case** (`work_mode`, `per_page`)
   - PHP: **camelCase** methods / arrays as appropriate for PSR-friendly APIs  
   Always map to wire **snake_case**.

---

## 5. Reliability and observability

1. Retry **idempotent GET** requests on `429` and `5xx` only.
2. Honor `Retry-After` when present; otherwise exponential backoff with jitter.
3. Configurable timeout with a sensible default (recommended: **30 seconds**).
4. Send `User-Agent: JobBox<Lang>SDK/<semver>` (examples: `JobBoxNodeSDK/0.1.0`, `JobBoxPhpSDK/0.1.0`, `JobBoxPythonSDK/0.1.0`). Optional app name suffix is allowed.
5. Never log full API keys. If logging is enabled, redact to the key prefix only.

---

## 6. Errors

1. One primary API error type per language (`JobBoxApiError`, `JobBoxApiException`, etc.).
2. Required fields: HTTP `status`, API `code` when present, human `message`.
3. Prefer also exposing optional `requestId` and raw `body` for debugging.
4. Distinguish network/timeout failures from HTTP API error responses when the language makes that natural.
5. Do not surface raw HTTP-client exceptions as the public failure mode for API responses.

---

## 7. Packaging and versioning

| Language | Package name (target) | Registry |
|----------|----------------------|----------|
| Node | `@getjobbox/sdk` | npm |
| PHP | `getjobbox/sdk` | Packagist (later) |
| Python | `getjobbox` or `getjobbox-sdk` | PyPI (later) |

1. Use semantic versioning; breaking wire or public API changes bump major.
2. Each package ships `CHANGELOG.md` and `LICENSE`.
3. Languages version independently (no forced lockstep releases).

### Node-specific packaging

- Dual ESM + CJS exports with TypeScript declarations
- `engines.node` ≥ 18
- `sideEffects: false` when accurate
- Publish surface limited via `files` (e.g. `dist`, README, LICENSE)
- Prefer zero runtime dependencies; use native `fetch` (injectable for tests)

---

## 8. Testing bar

1. Unit tests with **mocked HTTP** - no live API required for default CI.
2. Must cover:
   - Auth header presence and name
   - Query serialization (including CSV for arrays)
   - Success envelope unwrap (`data`)
   - Error mapping into the typed SDK error
   - Retry behavior for retryable status codes
3. Optional integration tests against staging must be **opt-in** (env flag / secret), never default-on in CI.

---

## 9. Documentation bar

### Language `README.md`

- Install instructions
- Auth via env `JOBBOX_API_KEY` (warn: never commit keys)
- Quickstart for `jobs.list`
- Link to [`../STANDARD.md`](./STANDARD.md)
- Error handling notes
- Jobs filter table (or link to Integration docs)

### Root `sdk/README.md`

- Language status matrix (Stable / In progress / Planned)
- Link to this standard
- Default base URL and auth header summary (one short paragraph max)

---

## 10. Conformance checklist (PRs)

Use this checklist when reviewing any language SDK PR:

- [ ] Lives under `sdk/<language>/` with README, CHANGELOG, LICENSE
- [ ] Does not import JobBox monorepo application code
- [ ] Sends `X-JobBox-Api-Key` on every SDK API call
- [ ] Resource client: `JobBox` → `jobs.*`
- [ ] Implements all v1 Jobs methods listed in §3
- [ ] Wire params are snake_case; multi-value filters are CSV
- [ ] Retries GETs on 429/5xx with backoff / Retry-After
- [ ] Sets `User-Agent` per §5
- [ ] Typed API error with status / code / message
- [ ] Mocked-HTTP unit tests covering §8
- [ ] README links to this standard and documents `JOBBOX_API_KEY`

---

## 11. Adding a new language

1. Read this standard end-to-end.
2. Create `sdk/<language>/` with a README stating status **Planned** or **In progress**.
3. Implement the **Jobs** resource only until other SDK HTTP modules exist.
4. Pass the conformance checklist before marking the package **Stable** in the root README matrix.
5. Prefer matching method semantics to existing Stable languages (Node is the reference implementation).

---

## 12. Reference implementation

- **Node (TypeScript):** [`sdk/node`](./node) - package `@getjobbox/sdk`
- HTTP surface implemented by JobBox API: `/api/v1/sdk/jobs*`
