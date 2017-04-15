#!/usr/bin/env bash

if [[ "${COMPOSER_HOME}" = "" ]]; then
    export COMPOSER_HOME="${HOME}/.composer"
fi

arguments=$@

if [[ $1 =~ ^(install|update|require|remove)$ ]]; then
    arguments="--ignore-platform-reqs ${arguments}"
fi

command="composer ${arguments}"
echo "Run in devtools container: ${command}"
docker-compose run devtools ${command}
