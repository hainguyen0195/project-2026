#!/usr/bin/env bash

COMI_MODERN_ROOT="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
export PATH="$COMI_MODERN_ROOT/.tools/bin:$PATH"
export COMPOSER_HOME="$COMI_MODERN_ROOT/.tools/composer"
export COMPOSER_CACHE_DIR="$COMI_MODERN_ROOT/.tools/composer/cache"
case ":${PHP_INI_SCAN_DIR:-}:" in
    *":$COMI_MODERN_ROOT/php-conf.d:"*) ;;
    *) export PHP_INI_SCAN_DIR="${PHP_INI_SCAN_DIR:-}:$COMI_MODERN_ROOT/php-conf.d" ;;
esac
