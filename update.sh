
#!/bin/bash
read_var() {
    if [ ! -f ".env" ]; then
        echo '.env file missing'
        return 1
    else
        local ENV_FILE='.env'
        local VAR=$(grep $1 "$ENV_FILE" | xargs)
        IFS="=" read -ra VAR <<< "$VAR"
        echo ${VAR[1]}
    fi
}

# Turn on maintenance mode
docker compose exec php php artisan down || true

# Pull the latest changes from the git repository
# git reset --hard
# git clean -df
git pull origin main

# Install/update composer dependecies
docker compose exec php composer install --no-progress --no-interaction

# Restart FPM
echo 'Restarting FPM...'
#docker compose php nginx-server nginx -s reload

# Run database migrations
docker compose exec php php artisan migrate --force

# Clear caches
docker compose exec php php artisan cache:clear

# Clear and cache routes
docker compose exec php php artisan route:cache

# Clear and cache config
docker compose exec php php artisan config:cache

# Clear and cache views
docker compose exec php php artisan view:cache

# Turn off maintenance mode
docker compose exec php php artisan up

# UPDATE FRONT END
frontend_path=$(read_var FRONTEND_PATH)
cd $frontend_path

git pull origin main

npm run build


