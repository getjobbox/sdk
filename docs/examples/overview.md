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

| Example | Stack | Default UI port | Default API port |
|---------|--------|-----------------|------------------|
| [Vue](vue.md) | Vue 3 + Vite | 5174 | (Node in-process) / 5175 Python |
| [React](react.md) | React + Vite | 5176 | (Node in-process) / 5177 Python |
| [Angular](angular.md) | Angular 19 | 5178 | 5179 Node / Python variant |

Each folder has its own README, `.env.example`, and setup steps.

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

## Quick start (any example)

```bash
cd examples/vue   # or react / angular
cp .env.example .env
# set JOBBOX_API_KEY from JobBox → Settings → Developer
npm install
npm run dev
```

Never commit `.env`.

Source: [github.com/getjobbox/sdk/tree/main/examples](https://github.com/getjobbox/sdk/tree/main/examples)
