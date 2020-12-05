#!/bin/bash

if [[ $(ls node_modules | wc -l) -eq 0 ]]
then cat <<EOM
NOTE : Dependencies not installed ('node_modules' empty), running 'npm install'.
EOM
  npm install
fi

# Add others warning messages here.

exec "$@"
