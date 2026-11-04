#!/usr/bin/env bash
set -euo pipefail
# Own, disposable GitHub-hosted runner only. Never run on a shared cPanel host.
[[ "${GITHUB_ACTIONS:-}" == 'true' && "${COMPOSE_PROJECT_NAME:-}" == *_ci ]]
mkdir -p week3-results
if ! command -v ufw >/dev/null; then
  sudo apt-get update -qq
  sudo apt-get install -y -qq ufw
fi
sudo ufw --force reset >/dev/null
sudo ufw default deny incoming >/dev/null
sudo ufw default allow outgoing >/dev/null
sudo ufw allow in on lo >/dev/null
sudo ufw deny 3306/tcp >/dev/null
sudo ufw --force enable >/dev/null
sudo ufw status verbose > week3-results/staging-firewall.txt
grep -q 'Status: active' week3-results/staging-firewall.txt
grep -q 'deny (incoming)' week3-results/staging-firewall.txt
docker compose config --format json | python3 -c '
import json,sys
x=json.load(sys.stdin)
assert not x["services"]["db"].get("ports"), "DB port must not be published"
assert all(p.get("host_ip")=="127.0.0.1" for p in x["services"]["app"]["ports"])
print("PASS: database is internal; app port is bound to loopback only.")
'
curl --fail --silent http://127.0.0.1:${APP_PORT:-8080}/health.php >/dev/null
echo 'PASS: UFW default-deny incoming, outbound allowed, DB deny rule and loopback health verified on disposable staging runner.'
