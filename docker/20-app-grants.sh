#!/bin/bash
set -eu
# Official MariaDB initialization creates the user first. Restrict its privileges
# after schema import; the application never needs DDL or root privileges.
[[ "$MARIADB_DATABASE" =~ ^[A-Za-z0-9_]+$ ]]
[[ "$MARIADB_USER" =~ ^[A-Za-z0-9_]+$ ]]
MYSQL_PWD="$MARIADB_ROOT_PASSWORD" mariadb --protocol=socket -uroot <<SQL
REVOKE ALL PRIVILEGES ON \`$MARIADB_DATABASE\`.* FROM '$MARIADB_USER'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON \`$MARIADB_DATABASE\`.* TO '$MARIADB_USER'@'%';
SQL
