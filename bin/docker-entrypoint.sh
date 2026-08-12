#!/bin/sh
set -eu

source_ca="${DATABASE_SSL_CA:-/etc/secrets/ca.pem}"
runtime_ca="/var/run/ems/ca.pem"

mkdir -p /var/run/ems
cp "$source_ca" "$runtime_ca"
chmod 0444 "$runtime_ca"
export DATABASE_SSL_CA="$runtime_ca"

php bin/check-runtime.php

sed -ri "s/^Listen .*/Listen ${PORT:-10000}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT:-10000}>/" \
    /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
