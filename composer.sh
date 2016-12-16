#!/usr/bin/env bash

if [ "$COMPOSER_HOME" = "" ]; then
    export COMPOSER_HOME="$HOME/.composer"
fi

docker-compose run devtools composer --ignore-platform-reqs "$@"
