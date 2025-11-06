#!/bin/sh
set -eu

# Required env vars:
# MYSQL_HOST, MYSQL_PORT, MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD
# REMOTE_USER, REMOTE_HOST, REMOTE_PATH

DATE="$(date +%Y%m%d_%H%M%S)"
TMP_DIR="/tmp"
OUT_FILE="${TMP_DIR}/${MYSQL_DATABASE}_${DATE}.sql.gz"

echo "[backup] Starting dump of ${MYSQL_DATABASE} at ${DATE}"
mysqldump \
  -h "${MYSQL_HOST:-mysql}" \
  -P "${MYSQL_PORT:-3306}" \
  -u "${MYSQL_USER}" \
  -p"${MYSQL_PASSWORD}" \
  --single-transaction --quick --lock-tables=false \
  "${MYSQL_DATABASE}" | gzip > "${OUT_FILE}"

echo "[backup] Dump complete: ${OUT_FILE}"
echo "[backup] Ensuring remote path exists: ${REMOTE_HOST}:${REMOTE_PATH}"
ssh -o StrictHostKeyChecking=no "${REMOTE_USER}@${REMOTE_HOST}" "mkdir -p '${REMOTE_PATH}'"

echo "[backup] Uploading to remote"
scp -o StrictHostKeyChecking=no "${OUT_FILE}" "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/"

echo "[backup] Uploaded. Cleaning up local temp file"
rm -f "${OUT_FILE}"
echo "[backup] Done"