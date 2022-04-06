#!/bin/bash
source .env
if [[ $APP_KEY == "" ]] ; then
	echo """Warning: no application encryption key has been defined.
	But this nice docker entrypoint will run php artisan key:generate for you."""
	php artisan key:generate
fi

if [[ ! -r 'storage/oauth-public.key' ]]
then cat <<EOM
WARNING ! Missing OAuth Public Key ! OAuth will not be available.
An empty key will be generated to let the server run.
You still can define this key by overriding the following file :
    storage/oauth-public.key
EOM
  echo > storage/oauth-public.key
fi

if [[ ! -d './vendor' || ! -r './vendor/autoload.php' ]]
then # FIXME : Let the user manually install dependencies, even if we know it will not run correctly ?
  cat <<EOM
NOTE : Dependencies not fully installed ('vendor/autoload.php' missing), running 'composer install --no-scripts'.
If you just cloned this project, make sure to run commands indicated in the README file.
EOM
  composer install --no-scripts
fi

# Add others warning messages here.

exec docker-php-entrypoint $@
