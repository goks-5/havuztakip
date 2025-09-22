php artisan down

df -lh / 

git reset --hard
git pull

## composer self-update --2 

## composer require microsoft/msphpsql

composer install --no-interaction --no-dev --prefer-dist

php artisan migrate --force

php artisan cache:clear

php artisan route:clear

php artisan config:clear

php artisan view:clear

php artisan up

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

## php artisan mysqlVarible:set  
