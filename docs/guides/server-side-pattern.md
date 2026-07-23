# Server-side pattern

**Do not call the JobBox SDK or put `JOBBOX_API_KEY` in frontend code.**

API keys must stay on the server. If you embed the key in a SPA, mobile app, or any public bundle, anyone can extract it and use your quota.

```text
Browser  →  your server (/api/*)  →  JobBox API (X-JobBox-Api-Key)
              ↑
         JOBBOX_API_KEY lives here only
```

---

## Recommended architecture

1. The browser talks only to **your** `/api/*` routes (no JobBox key in the client).
2. A small Node, Python, PHP, or Next.js Route Handler holds `JOBBOX_API_KEY` and calls the SDK.
3. The UI never imports `@getjobbox/sdk` / `getjobbox` / `GetJobBox\JobBox` or reads the secret.

Official sample boards follow this pattern:

- **JS / TS:** [Vue](../examples/vue.md), [React](../examples/react.md), [Angular](../examples/angular.md), [Next.js](../examples/next.md)
- **PHP:** [CodeIgniter](../examples/codeigniter.md), [Laravel](../examples/laravel.md)

---

## Minimal Node proxy

=== "Node"

    ```ts
    // server.ts (Express-style sketch)
    import express from 'express';
    import { JobBox } from '@getjobbox/sdk';

    const jobbox = new JobBox({
      apiKey: process.env.JOBBOX_API_KEY!,
      baseUrl: process.env.JOBBOX_BASE_URL,
    });

    const app = express();

    app.get('/api/jobs', async (req, res) => {
      try {
        const data = await jobbox.jobs.list({
          search: typeof req.query.search === 'string' ? req.query.search : undefined,
          category: typeof req.query.category === 'string' ? req.query.category : undefined,
          page: Number(req.query.page) || 1,
          perPage: Number(req.query.perPage) || 28,
        });
        res.json(data);
      } catch (err) {
        res.status(502).json({ error: 'Failed to list jobs' });
      }
    });

    app.get('/api/jobs/:id', async (req, res) => {
      try {
        const data = await jobbox.jobs.get(req.params.id);
        res.json(data);
      } catch (err) {
        res.status(502).json({ error: 'Failed to get job' });
      }
    });

    app.listen(5175);
    ```

=== "Python"

    ```python
    # Flask-style sketch
    import os
    from flask import Flask, jsonify, request
    from getjobbox import JobBox

    jobbox = JobBox(api_key=os.environ["JOBBOX_API_KEY"])
    app = Flask(__name__)

    @app.get("/api/jobs")
    def list_jobs():
        data = jobbox.jobs.list(
            search=request.args.get("search"),
            category=request.args.get("category"),
            page=int(request.args.get("page", 1)),
            per_page=int(request.args.get("per_page", 28)),
        )
        return jsonify(data)

    @app.get("/api/jobs/<job_id>")
    def get_job(job_id: str):
        return jsonify(jobbox.jobs.get(job_id))
    ```

=== "PHP"

    ```php
    <?php
    // public/api/jobs.php sketch
    use GetJobBox\JobBox;

    $jobbox = new JobBox(['apiKey' => getenv('JOBBOX_API_KEY') ?: '']);

    header('Content-Type: application/json');

    $search = $_GET['search'] ?? null;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

    $params = ['page' => $page, 'perPage' => 28];
    if (is_string($search) && $search !== '') {
        $params['search'] = $search;
    }

    echo json_encode($jobbox->jobs->list($params));
    ```

The browser then calls `/api/jobs?search=react` - never JobBox directly.

Full framework demos: [CodeIgniter](../examples/codeigniter.md), [Laravel](../examples/laravel.md). For App Router Route Handlers, see [Next.js](../examples/next.md).

---

## Frontend responsibilities

- Render lists, filters, and detail modals from **your** API JSON.
- Forward user filter inputs as query params to your proxy.
- Link “Apply” to the JobBox app URL (e.g. `https://app.getjobbox.com/j/:id`) if that is your product flow - without shipping the partner key.

---

## Checklist

- [ ] `JOBBOX_API_KEY` only in server env / secrets
- [ ] `.env` gitignored; `.env.example` has placeholders only
- [ ] No SDK imports in Vite/Webpack client bundles
- [ ] Proxy validates / clamps `page` and `perPage` before calling the SDK
- [ ] Errors from JobBox are mapped to safe client-facing messages (avoid leaking raw internals)
