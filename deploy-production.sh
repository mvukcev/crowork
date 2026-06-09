#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

echo "[deploy] Rebuilding frontend assets..."
bash scripts/rebuild-assets.sh

echo "[deploy] Rebuilding Laravel caches after asset build..."
php artisan optimize:clear
php artisan optimize
php artisan view:cache

echo "[deploy] Deployment asset/cache pipeline completed."