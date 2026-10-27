#!/usr/bin/env bash
# Runs on the server. Never removes the database volume or overwrites secrets.
set -euo pipefail
path="${1:?Missing deployment directory}"
revision="${2:?Missing revision}"
[[ "$path" =~ ^/[A-Za-z0-9_./-]+$ && "$path" != / && "$path" != *..* ]]
[[ "$revision" =~ ^[a-f0-9]{40}$ ]]
cd "$path"
test -f .env
for secret in db_password db_root_password admin_password; do
    test -s ".secrets/$secret"
done
release="deployment-state/releases/$revision"
mkdir -p "$release"
tar -xf "incoming-$revision.tar" -C "$release"
gzip -dc "$release/app-image.tar.gz" | docker load
mkdir -p database docker
cp "$release/docker-compose.yml" docker-compose.yml
cp "$release/database/schema.sql" database/schema.sql
cp "$release/docker/20-app-grants.sh" docker/20-app-grants.sh
umask 077
printf 'APP_IMAGE=devopqlyfood:%s\nAPP_REVISION=%s\n' "$revision" "$revision" > deployment.env.next
compose=(docker compose --project-name devopqlyfood --env-file .env --env-file deployment.env.next)
if ! "${compose[@]}" up -d --no-build --wait --wait-timeout 180; then
    echo 'Deployment health check failed.' >&2
    if test -f deployment.env; then
        docker compose --project-name devopqlyfood --env-file .env --env-file deployment.env up -d --no-build --wait --wait-timeout 180
        echo 'Previous application image restored; database volume retained.' >&2
    fi
    exit 1
fi
"${compose[@]}" exec -T app php -r '$v=json_decode(file_get_contents("http://127.0.0.1/version.php"),true);exit(($v["revision"]??null)===$argv[1]?0:1);' "$revision"
if test -f deployment.env; then
    cp deployment.env deployment-state/previous.env
fi
mv deployment.env.next deployment.env
printf '%s\n' "$revision" > deployment-state/current-revision
rm -f "incoming-$revision.tar" "$release/app-image.tar.gz"
echo "Deployment healthy at commit $revision. Database volume retained."
