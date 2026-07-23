# Laravel example

Laravel job board — same JobFinder board as the [Vue](vue.md) / [CodeIgniter](codeigniter.md) examples, powered by the **PHP SDK** (`getjobbox/sdk`).

The API key stays on the server. Controllers expose the same local `/api/*` contract; Blade + vanilla JS render the UI.

---

## Setup

```bash
cd examples/laravel
cp .env.example .env
php artisan key:generate
# set JOBBOX_API_KEY from JobBox → Settings → Developer
composer install
```

| Vue `.env` | Laravel `.env` |
|------------|----------------|
| `JOBBOX_API_KEY` | `JOBBOX_API_KEY` |
| `JOBBOX_BASE_URL` | `JOBBOX_BASE_URL` |
| `VITE_JOBBOX_APP_URL` | `JOBBOX_APP_URL` |

The PHP package is linked via Composer path repo (`../../php`).

---

## Run

```bash
php artisan serve --port=8081
```

- UI: [http://localhost:8081](http://localhost:8081)
- Health: `/api/health` → `"sdk":"php"`, `"example":"laravel"`

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

- [Laravel](https://laravel.com/) 13
- [`getjobbox/sdk`](https://github.com/getjobbox/sdk/tree/main/php)
- Blade layouts + the same JobFinder JS/CSS as CodeIgniter

## Notes

- Default port **8081** (CodeIgniter uses **8080**).
- No database is required for the job board (`.env.example` uses `file` sessions / `sync` queue).

Repo path: [`examples/laravel`](https://github.com/getjobbox/sdk/tree/main/examples/laravel)
