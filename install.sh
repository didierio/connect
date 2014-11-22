#!/bin/bash
set -e

if [ -z "$1" ]; then
  env="dev"
else
  env=$1
fi

cd "`dirname "$0"`"

if [ ! -f app/config/parameters.yml ]; then
    cp app/config/parameters.yml.dist app/config/parameters.yml
fi

if [ ! -f composer.phar ]; then
    curl -s http://getcomposer.org/installer | php
fi

echo ">>> Installing vendors"
php composer.phar install

echo ">>> Removing cache"
rm -rf app/cache/* app/logs/*

echo ">>> Installing assets"
./app/console assets:install --symlink --env=$env

php app/console doctrine:database:drop --force --env=$env || true

echo ">>> Creating database"
php app/console doctrine:database:create --env=$env

echo ">>> Creating tables"
php app/console doctrine:schema:create --env=$env

echo ">>> Importing fixtures"
./app/console doctrine:fixtures:load --append --env=$env

echo ">>> Building assets"
./app/console assetic:dump --env=prod --no-debug --env=$env
