# CodeIgniter example

CodeIgniter 4 job board — same JobFinder board as the [Vue](vue.md) example, powered by the **PHP SDK** (`getjobbox/sdk`).

The API key stays on the server. Controllers expose the same local `/api/*` contract; Blade + vanilla JS render the UI.

---

## Setup

```bash
cd examples/codeigniter
cp .env.example .env
# set JOBBOX_API_KEY from JobBox → Settings → Developer
composer install
```

| Vue `.env` | CodeIgniter `.env` |
|------------|--------------------|
| `JOBBOX_API_KEY` | `JOBBOX_API_KEY` |
| `JOBBOX_BASE_URL` | `JOBBOX_BASE_URL` |
| `VITE_JOBBOX_APP_URL` | `JOBBOX_APP_URL` |

The PHP package is linked via Composer path repo (`../../php`).

---

## Run

```bash
composer serve
# or: php spark serve --port 8080
```

- UI: [http://localhost:8080](http://localhost:8080)
- Health: `/api/health` → `"sdk":"php"`

---

## What it shows

| Route | Page | SDK call |
|-------|------|----------|
| `/` | All Jobs | `jobs->list` + category chips |
| `/hr` | HR Jobs | `jobs->list(['category' => 'hr'])` |
| `GET /api/categories` | — | `jobs->categories()` |
| `GET /api/jobs` | — | `jobs->list(...)` |
| `GET /api/jobs/{id}` | — | `jobs->get($id)` |

Includes search, category filters, pagination, and a job detail modal with **Apply on JobBox** → `{JOBBOX_APP_URL}/j/:id`.

---

## Stack

- [CodeIgniter 4](https://codeigniter.com/)
- [`getjobbox/sdk`](https://github.com/getjobbox/sdk/tree/main/php)
- JobFinder CSS + `public/js/jobs.js` (same surface as the SPA examples)

Repo path: [`examples/codeigniter`](https://github.com/getjobbox/sdk/tree/main/examples/codeigniter)
