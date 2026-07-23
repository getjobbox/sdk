# Next.js example

Next.js App Router job board — same JobFinder board as the [React](react.md) / [Vue](vue.md) examples.

Uses `@getjobbox/sdk` only in **Route Handlers**. The browser never sees `JOBBOX_API_KEY`.

---

## Setup

```bash
cd examples/next
cp .env.example .env.local
# set JOBBOX_API_KEY from JobBox → Settings → Developer
npm install
```

| Vue / React env | Next.js `.env.local` |
|-----------------|----------------------|
| `JOBBOX_API_KEY` | `JOBBOX_API_KEY` |
| `JOBBOX_BASE_URL` | `JOBBOX_BASE_URL` |
| `VITE_JOBBOX_APP_URL` | `NEXT_PUBLIC_JOBBOX_APP_URL` |

Default UI port is **3001** (avoids clashes with other local apps on 3000).

---

## Run

```bash
npm run dev
```

- UI: [http://localhost:3001](http://localhost:3001)
- Health: `/api/health` → `"sdk":"node"`, `"example":"next"`

SDK calls live in `app/api/**/route.ts` via a small `lib/jobbox.ts` helper.

---

## What it shows

| Route | Page | SDK call |
|-------|------|----------|
| `/` | All Jobs | `jobs.list` + category chips |
| `/hr` | HR Jobs | `jobs.list({ category: 'hr' })` |
| `GET /api/categories` | — | `jobs.categories()` |
| `GET /api/jobs` | — | `jobs.list(...)` |
| `GET /api/jobs/[id]` | — | `jobs.get(id)` |

Includes search, category filters, pagination, client-side list/detail cache, and a job detail modal with **Apply on JobBox** → `{NEXT_PUBLIC_JOBBOX_APP_URL}/j/:id`.

---

## Notes

- No Python proxy mode in this example — Node SDK only (idiomatic Next Route Handlers).
- Do not import `@getjobbox/sdk` in Client Components.

Repo path: [`examples/next`](https://github.com/getjobbox/sdk/tree/main/examples/next)
