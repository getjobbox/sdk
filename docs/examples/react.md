# React example

React + Vite job board — same JobFinder board as the [Vue example](vue.md), built with React and React Router.

The API key stays on the server. The browser only calls local `/api/*` routes.

---

## Setup

```bash
cd examples/react
cp .env.example .env
# set JOBBOX_API_KEY from JobBox → Settings → Developer
npm install
```

`JOBBOX_BASE_URL` defaults to `https://api.getjobbox.com`.  
Default UI port is **5176** (Vue uses 5174).

---

## Run with Node SDK (default)

```bash
npm run dev
```

- UI: [http://localhost:5176](http://localhost:5176)
- Health: `/api/health` → `"sdk":"node"`

---

## Run with Python SDK

```bash
python3 -m pip install -e ../../python
npm run dev:python
```

Python API on **5177**, Vite UI on **5176**.

---

## What it shows

| Route | Page | SDK call |
|-------|------|----------|
| `/` | All Jobs | `jobs.list` + category chips |
| `/hr` | HR Jobs | `jobs.list({ category: 'hr' })` |
| `GET /api/categories` | — | `jobs.categories()` |
| `GET /api/jobs` | — | `jobs.list(...)` |
| `GET /api/jobs/:id` | — | `jobs.get(id)` |

---

## Tests

```bash
npm test
```

Repo path: [`examples/react`](https://github.com/getjobbox/sdk/tree/main/examples/react)
