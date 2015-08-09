#!/bin/bash
set -e

if [ -z "$1" ]; then
  env="dev"
else
  env=$1
fi

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
