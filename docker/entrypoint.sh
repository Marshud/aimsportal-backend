#!/bin/bash

if [ ! -f "vendor/autoload.php" ]; then
    composer install --no-progress --no-interaction
fi

if [ ! -f ".env" ]; then
    echo "Creating env file for env $APP_ENV"
    cp .env.example .env
    php artisan key:generate
else
    echo "env file exists."
fi

php artisan config:clear
php artisan view:clear
php artisan cache:clear
php artisan migrate --force
php artisan db:seed --force

if [ ! -f "storage/app/codelists_imported.txt" ]; then
    php artisan iati:import-codelists
fi

if [ ! -f "storage/app/ss_projects_imported.txt" ]; then
    php artisan db:seed --force --class=IatiProjectsSeeder
fi

php-fpm -D
nginx -g "daemon off;"
