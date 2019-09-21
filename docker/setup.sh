#!/bin/bash

if [[ "$0" == '/entrypoint.sh' && -d './docker' ]]; then
  echo First run of this container, installing database...
  if (
    composer install && \
    php artisan migrate:refresh --seed --force && \
    php artisan portail:clear
  )
  then rm -r ./docker # Mark setup as complete
  else exit 2
  fi
fi

if [[ ! -r 'storage/oauth-public.key' ]]; then
  echo "WARNING ! Missing OAuth Public Key ! OAuth will not be available." >&2
  # Generating an empty file to avoid 400 with the API
  echo > storage/oauth-public.key
fi

exec docker-php-entrypoint $@
