#!/bin/sh
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if [ ! -f .env ]; then
    cp .env.example .env
fi

if ! grep -q '^APP_KEY=base64' .env; then
    php artisan key:generate --force
fi

echo "Aguardando o banco de dados..."
until php -r 'try { new PDO("mysql:host=".getenv("DB_HOST").";port=".getenv("DB_PORT"), getenv("DB_USERNAME"), getenv("DB_PASSWORD")); exit(0); } catch (Exception $e) { exit(1); }'; do
    sleep 2
done

php artisan migrate --force

if [ ! -f storage/app/.seeded ]; then
    php artisan db:seed --force
    touch storage/app/.seeded
fi

chown -R www-data:www-data storage bootstrap/cache

exec "$@"