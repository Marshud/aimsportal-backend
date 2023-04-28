
# Turn on maintenance mode
docker compose php php artisan down || true

# Pull the latest changes from the git repository
# git reset --hard
# git clean -df
git pull origin main

# Install/update composer dependecies
docker compose php composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Restart FPM
echo 'Restarting FPM...'
#docker compose php nginx-server nginx -s reload

# Run database migrations
docker compose php php artisan migrate --force

# Clear caches
docker compose php php artisan cache:clear

# Clear and cache routes
docker compose php php artisan route:cache

# Clear and cache config
docker compose php php artisan config:cache

# Clear and cache views
docker compose php php artisan view:cache

# Turn off maintenance mode
docker compose php php artisan up
