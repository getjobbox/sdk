# Authentication

Partner SDKs authenticate with a **JobBox API key**. The SDK sends it on every request as:

```http
X-JobBox-Api-Key: <your_plaintext_key>
```

JWT session tokens are **not** used for the SDK Jobs surface.

!!! danger "Never commit keys"
    Store keys in environment variables or a secrets manager. Never commit plaintext keys to git, or ship them in frontend / mobile bundles. See [Server-side pattern](../guides/server-side-pattern.md).

---

## Create an API key

Keys are created via the authenticated REST API (session JWT), not by the Jobs SDK itself.

```http
POST /api/v1/api-keys
Authorization: Bearer <jwt>
Content-Type: application/json

{ "name": "my-app" }
```

The response includes a **plaintext key shown once**. Copy it immediately and store it as `JOBBOX_API_KEY`.

You can also create and manage keys in the JobBox product UI under **Settings → Developer**.

---

## Store the key

Export it in your shell or put it in a local `.env` (gitignored):

```bash
export JOBBOX_API_KEY=jb_live_xxxxxxxx
```

Example `.env` for a server process:

```ini
JOBBOX_API_KEY=jb_live_xxxxxxxx
JOBBOX_BASE_URL=https://api.getjobbox.com
```

---

## Pass the key into the client

=== "Node"

    ```ts
    import { JobBox } from '@getjobbox/sdk';

    const jobbox = new JobBox({
      apiKey: process.env.JOBBOX_API_KEY!,
    });
    ```

=== "Python"

    ```python
    import os
    from getjobbox import JobBox

    jobbox = JobBox(api_key=os.environ["JOBBOX_API_KEY"])
    ```

=== "PHP"

    ```php
    <?php
    use GetJobBox\JobBox;

    $jobbox = new JobBox([
        'apiKey' => getenv('JOBBOX_API_KEY') ?: '',
    ]);
    ```

If the key is missing or empty, the client constructor throws before any HTTP call.

---

## What the SDK sends

On every Jobs request the HTTP client adds:

| Header | Value |
|--------|--------|
| `X-JobBox-Api-Key` | Your plaintext key |
| `User-Agent` | `JobBox{Lang}SDK/<semver>` (optional app name suffix) |
| `Accept` | `application/json` |

---

## Next steps

- [Configuration](configuration.md)  
- [Quickstart](quickstart.md)
