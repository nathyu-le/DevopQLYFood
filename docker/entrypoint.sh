#!/bin/sh
set -eu
php /usr/local/bin/configure-container.php
exec docker-php-entrypoint "$@"
