#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
OUTPUT_DIR="$ROOT_DIR/apps/mobile-android/app/src/main/java/com/haida/hubapp/api"
CONFIG_FILE="$ROOT_DIR/tools/openapi/configs/openapi-generator/kotlin-retrofit.yaml"
OPENAPI_URL="${OPENAPI_URL:-http://localhost/api/v1/app/openapi}"

GENERATOR_BIN="${OPENAPI_GENERATOR_CLI:-openapi-generator-cli}"

if [ -n "${OPENAPI_GENERATOR_JAR:-}" ]; then
  java -jar "$OPENAPI_GENERATOR_JAR" generate -g kotlin -i "$OPENAPI_URL" -o "$OUTPUT_DIR" -c "$CONFIG_FILE"
else
  "$GENERATOR_BIN" generate -g kotlin -i "$OPENAPI_URL" -o "$OUTPUT_DIR" -c "$CONFIG_FILE"
fi

printf "Generated Kotlin client at %s\n" "$OUTPUT_DIR"
