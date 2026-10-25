#!/usr/bin/env sh
set -eu
cd "$(dirname "$0")"
php -S 127.0.0.1:8000 -t public public/router.php
