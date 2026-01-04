#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
OUTPUT_DIR="$ROOT_DIR/apps/web-pwa/src/lib/api-client"
CONFIG_FILE="$ROOT_DIR/tools/openapi/configs/openapi-generator/typescript-fetch.yaml"
OPENAPI_URL="${OPENAPI_URL:-http://localhost/api/v1/app/openapi}"

GENERATOR_BIN="${OPENAPI_GENERATOR_CLI:-openapi-generator-cli}"

if [ -n "${OPENAPI_GENERATOR_JAR:-}" ]; then
  java -jar "$OPENAPI_GENERATOR_JAR" generate -g typescript-fetch -i "$OPENAPI_URL" -o "$OUTPUT_DIR" -c "$CONFIG_FILE"
else
  "$GENERATOR_BIN" generate -g typescript-fetch -i "$OPENAPI_URL" -o "$OUTPUT_DIR" -c "$CONFIG_FILE"
fi

printf "Generated TS client at %s\n" "$OUTPUT_DIR"
