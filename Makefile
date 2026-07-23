# JobBox SDK - run `make` or `make help`
.DEFAULT_GOAL := help

.PHONY: help docs docs-build docs-install

help: ## Show available commands
	@echo "JobBox SDK"
	@echo ""
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z0-9_-]+:.*?## / {printf "  %-20s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

docs-install: ## Install MkDocs deps (pip)
	python3 -m pip install -r requirements-docs.txt

docs: ## Serve SDK docs locally (http://127.0.0.1:8000)
	python3 -m mkdocs serve -a 127.0.0.1:8000

docs-build: ## Build static docs into site/
	python3 -m mkdocs build --strict
