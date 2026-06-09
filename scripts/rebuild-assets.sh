#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

echo "[assets] Cleaning stale Vite state..."
rm -f public/hot
rm -f storage/framework/vite.hot
rm -rf public/build

echo "[assets] Installing dependencies and building..."
npm ci
npm run build

if [[ ! -f public/build/manifest.json ]]; then
  echo "[assets] ERROR: public/build/manifest.json is missing after build."
  exit 1
fi

echo "[assets] Build complete and manifest verified."