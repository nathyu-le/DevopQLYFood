#!/usr/bin/env bash
# Runs on the GitHub-hosted runner. Transfers the already-tested image over SSH.
set -euo pipefail
: "${DEPLOY_HOST:?Missing DEPLOY_HOST}"
: "${DEPLOY_USER:?Missing DEPLOY_USER}"
: "${DEPLOY_PATH:?Missing DEPLOY_PATH}"
: "${DEPLOY_SSH_KEY:?Missing DEPLOY_SSH_KEY}"
: "${DEPLOY_KNOWN_HOSTS:?Missing DEPLOY_KNOWN_HOSTS}"
: "${DEPLOY_REVISION:?Missing DEPLOY_REVISION}"
DEPLOY_PORT="${DEPLOY_PORT:-22}"
[[ "$DEPLOY_HOST" =~ ^[A-Za-z0-9][A-Za-z0-9.-]*$ ]]
[[ "$DEPLOY_USER" =~ ^[a-z_][a-z0-9_-]*$ ]]
[[ "$DEPLOY_PATH" =~ ^/[A-Za-z0-9_./-]+$ && "$DEPLOY_PATH" != / && "$DEPLOY_PATH" != *..* ]]
[[ "$DEPLOY_PORT" =~ ^[0-9]{1,5}$ && "$DEPLOY_PORT" -gt 0 && "$DEPLOY_PORT" -le 65535 ]]
[[ "$DEPLOY_REVISION" =~ ^[a-f0-9]{40}$ ]]
test -s app-image.tar.gz

temporary="$(mktemp -d)"
trap 'rm -rf "$temporary"' EXIT
umask 077
printf '%s\n' "$DEPLOY_SSH_KEY" > "$temporary/key"
printf '%s\n' "$DEPLOY_KNOWN_HOSTS" > "$temporary/known_hosts"
options=(-i "$temporary/key" -o "UserKnownHostsFile=$temporary/known_hosts" -o StrictHostKeyChecking=yes -o BatchMode=yes -o ConnectTimeout=20)
target="$DEPLOY_USER@$DEPLOY_HOST"

ssh "${options[@]}" -p "$DEPLOY_PORT" "$target" "test -f '$DEPLOY_PATH/.env' && test -d '$DEPLOY_PATH/.secrets' && docker compose version >/dev/null"
tar -cf "$temporary/release.tar" app-image.tar.gz docker-compose.yml database/schema.sql docker/20-app-grants.sh
scp "${options[@]}" -P "$DEPLOY_PORT" "$temporary/release.tar" "$target:$DEPLOY_PATH/incoming-$DEPLOY_REVISION.tar"
ssh "${options[@]}" -p "$DEPLOY_PORT" "$target" "bash -s -- '$DEPLOY_PATH' '$DEPLOY_REVISION'" < tools/apply-release.sh
