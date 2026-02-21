#!/usr/bin/env bash
set -euo pipefail

if [[ "${APP_ENV:-}" == "production" ]]; then
  echo "Refusing to run browser E2E in production." >&2
  exit 1
fi

if [[ ! -d "node_modules/@playwright/test" ]]; then
  echo "[browser-e2e] installing root node dependencies..."
  npm install
fi

if command -v google-chrome >/dev/null 2>&1 || command -v google-chrome-stable >/dev/null 2>&1; then
  echo "[browser-e2e] system chrome detected, skip playwright browser download."
else
  echo "[browser-e2e] installing playwright browser (chromium)..."
  npx playwright install chromium
fi

echo "[browser-e2e] running filament panel browser E2E..."
npm run test:e2e:panel

if [[ -f "apps/web-pwa/package.json" ]]; then
  if [[ ! -d "apps/web-pwa/node_modules" ]]; then
    echo "[browser-e2e] installing apps/web-pwa node dependencies..."
    npm --prefix apps/web-pwa install
  fi
  echo "[browser-e2e] running web-pwa browser E2E..."
  npm --prefix apps/web-pwa run test:e2e
fi

echo "[browser-e2e] done."
