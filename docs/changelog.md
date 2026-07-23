# Changelog

Language packages version independently. See each package’s changelog in the repository:

| Language | Changelog |
|----------|-----------|
| Node (`@getjobbox/sdk`) | [node/CHANGELOG.md](https://github.com/getjobbox/sdk/blob/main/node/CHANGELOG.md) |
| Python (`getjobbox`) | [python/CHANGELOG.md](https://github.com/getjobbox/sdk/blob/main/python/CHANGELOG.md) |
| PHP (`getjobbox/sdk`) | [php/CHANGELOG.md](https://github.com/getjobbox/sdk/blob/main/php/CHANGELOG.md) |

## Release tags

| Language | Tag pattern | Example |
|----------|-------------|---------|
| Node | `node-vX.Y.Z` | `node-v0.1.0` |
| Python | `python-vX.Y.Z` | `python-v0.1.0` |
| PHP | `php-vX.Y.Z` | `php-v0.1.0` |

Publishing is handled by the [publish workflow](https://github.com/getjobbox/sdk/blob/main/.github/workflows/publish.yml):

- **Node** → GitHub Packages  
- **Python / PHP** → GitHub Release assets  

Cross-language contract changes are tracked in [STANDARD.md](https://github.com/getjobbox/sdk/blob/main/STANDARD.md).

## Docs site

Example apps documented under **Examples** in this site:

- JS/TS: Vue, React, Angular, Next.js
- PHP: CodeIgniter, Laravel
