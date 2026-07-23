# Installation

Install the JobBox SDK for your runtime. All packages are currently at **v0.1.0**.

!!! tip "Requirements"
    - **Node:** ≥ 18  
    - **Python:** ≥ 3.10  
    - **PHP:** ≥ 8.1 with `ext-curl` and `ext-json`

---

## Node.js (TypeScript)

Package: **`@getjobbox/sdk`**

### Option A - GitHub Packages (recommended)

Published on [GitHub Packages](https://github.com/orgs/getjobbox/packages). Add a project `.npmrc`:

```ini
@getjobbox:registry=https://npm.pkg.github.com
//npm.pkg.github.com/:_authToken=${GITHUB_TOKEN}
```

`GITHUB_TOKEN` (or a personal access token) needs the `read:packages` scope. Then:

```bash
npm install @getjobbox/sdk
```

Or with Yarn / pnpm:

```bash
yarn add @getjobbox/sdk
# or
pnpm add @getjobbox/sdk
```

### Option B - Install from Git

Install directly from the public SDK repository (subdirectory `node/`):

```bash
npm install git+https://github.com/getjobbox/sdk.git#main:node
```

Pin to a release tag when you want a fixed version:

```bash
npm install git+https://github.com/getjobbox/sdk.git#node-v0.1.0:node
```

### Option C - Local path (development)

If you have the repo cloned:

```bash
npm install file:../path/to/sdk/node
```

The sample apps under `examples/` use this pattern (`"@getjobbox/sdk": "file:../../node"`). See [Vue](../examples/vue.md), [React](../examples/react.md), [Angular](../examples/angular.md), and [Next.js](../examples/next.md).

### Verify

```ts
import { JobBox, VERSION } from '@getjobbox/sdk';

console.log(VERSION); // e.g. "0.1.0"
```

The package ships dual **ESM + CJS** builds with TypeScript declarations. Zero runtime dependencies (uses native `fetch`).

---

## Python

Package: **`getjobbox`**

### Option A - GitHub Releases wheel (recommended)

```bash
pip install "https://github.com/getjobbox/sdk/releases/download/python-v0.1.0/getjobbox-0.1.0-py3-none-any.whl"
```

When a newer release is published, bump the tag and wheel name in the URL (see [Releasing](https://github.com/getjobbox/sdk#releasing)).

### Option B - From a clone (editable)

```bash
git clone https://github.com/getjobbox/sdk.git
cd sdk/python
pip install -e .
# with test deps:
pip install -e ".[dev]"
```

### Verify

```python
from getjobbox import JobBox, VERSION

print(VERSION)  # e.g. "0.1.0"
```

Zero runtime dependencies (stdlib `urllib`).

---

## PHP

Package: **`getjobbox/sdk`**

### Option A - Composer + GitHub Release zip (recommended)

In your app `composer.json`:

```json
{
  "repositories": [
    {
      "type": "package",
      "package": {
        "name": "getjobbox/sdk",
        "version": "0.1.0",
        "dist": {
          "url": "https://github.com/getjobbox/sdk/releases/download/php-v0.1.0/getjobbox-sdk-php-0.1.0.zip",
          "type": "zip"
        },
        "autoload": {
          "psr-4": {
            "GetJobBox\\": "src/"
          }
        }
      }
    }
  ],
  "require": {
    "getjobbox/sdk": "0.1.0"
  }
}
```

Then:

```bash
composer update getjobbox/sdk
```

Bump `version` and the release download URL when upgrading.

### Option B - From a clone

```bash
git clone https://github.com/getjobbox/sdk.git
cd sdk/php
composer install
```

Sample apps that path-require this package: [CodeIgniter](../examples/codeigniter.md), [Laravel](../examples/laravel.md).

### Verify

```php
<?php
require 'vendor/autoload.php';

use GetJobBox\JobBox;

$jobbox = new JobBox(['apiKey' => getenv('JOBBOX_API_KEY') ?: '']);
```

Requires **PHP ≥ 8.1** with `ext-curl` and `ext-json`. Zero runtime Composer dependencies.

---

## Versioning and upgrades

Languages version independently. Release tags are language-prefixed:

| Language | Tag example | Asset |
|----------|-------------|--------|
| Node | `node-v0.1.0` | GitHub Packages |
| Python | `python-v0.1.0` | `.whl` / sdist on Releases |
| PHP | `php-v0.1.0` | zip on Releases |

See the repo [CHANGELOG](../changelog.md) links for each language.

## Next steps

- [Authentication](authentication.md) - create and store an API key  
- [Configuration](configuration.md) - client options  
- [Quickstart](quickstart.md) - first `jobs.list` call
