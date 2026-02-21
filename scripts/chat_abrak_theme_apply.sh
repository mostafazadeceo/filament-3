#!/usr/bin/env bash
set -euo pipefail

TARGET_HOST="${1:-10.10.0.35}"
MONGO_CONTAINER="${2:-rocketchat-mongo-1}"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

ssh -o BatchMode=yes -o StrictHostKeyChecking=no "root@${TARGET_HOST}" \
  "docker exec -i ${MONGO_CONTAINER} mongosh --quiet" < "${SCRIPT_DIR}/chat_abrak_theme_apply.js"
