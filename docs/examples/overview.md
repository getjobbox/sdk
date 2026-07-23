# Examples overview

Sample job boards that show how to use the JobBox partner SDKs behind a small local API.

!!! danger "Server-side keys only"
    Do not put `JOBBOX_API_KEY` in frontend code. See [Server-side pattern](../guides/server-side-pattern.md).

```text
Browser  →  your server (/api/*)  →  JobBox API
              ↑
         JOBBOX_API_KEY here only
```

---

## Available examples

### JavaScript / TypeScript

| Example | Stack | Default port | SDK |
|---------|--------|--------------|-----|
| [Vue](vue.md) | Vue 3 + Vite | 5174 | Node (in-process) / Python on 5175 |
| [React](react.md) | React + Vite | 5176 | Node (in-process) / Python on 5177 |
| [Angular](angular.md) | Angular 19 | 5178 | Node / Python on 5179 |
| [Next.js](next.md) | Next.js App Router | 3001 | Node Route Handlers |

### PHP

| Example | Stack | Default port | SDK |
|---------|--------|--------------|-----|
| [CodeIgniter](codeigniter.md) | CodeIgniter 4 | 8080 | `getjobbox/sdk` |
| [Laravel](laravel.md) | Laravel 13 | 8081 | `getjobbox/sdk` |

Each folder has its own README, `.env.example` (or `.env.local` for Next), and setup steps.

---

## What they demonstrate

| Feature | SDK call |
|---------|----------|
| Search + category chips | `jobs.list`, `jobs.categories` |
| Pagination | `jobs.list({ page, perPage })` |
| Job detail modal | `jobs.get(id)` |
| HR locked route | `jobs.list({ category: 'hr' })` |
| Apply on JobBox | Link to `app.getjobbox.com/j/:id` |

Advanced filters (`workMode`, salary, dates, …) are fully supported by the SDK; the sample UIs focus on search, category, and pagination. Extend your proxy to forward more query params as needed — see [Search and filters](../guides/search-and-filters.md).

---

## Quick start

=== "SPA (Vue / React / Angular)"

    ```bash
    cd examples/vue   # or react / angular
    cp .env.example .env
    # set JOBBOX_API_KEY
    npm install
    npm run dev
    ```

=== "Next.js"

    ```bash
    cd examples/next
    cp .env.example .env.local
    # set JOBBOX_API_KEY
    npm install
    npm run dev
    ```

=== "CodeIgniter"

    ```bash
    cd examples/codeigniter
    cp .env.example .env
    # set JOBBOX_API_KEY
    composer install
    composer serve
    ```

=== "Laravel"

    ```bash
    cd examples/laravel
    cp .env.example .env
    php artisan key:generate
    # set JOBBOX_API_KEY
    composer install
    php artisan serve --port=8081
    ```

Never commit `.env` / `.env.local`.

Source: [github.com/getjobbox/sdk/tree/main/examples](https://github.com/getjobbox/sdk/tree/main/examples)
