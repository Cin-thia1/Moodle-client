#!/bin/bash
set -e

echo ">> Attente de la base de données ($DB_HOST)..."
until mysqladmin ping -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent; do
  sleep 2
done
echo ">> Base de données prête."

# Crée le .env si absent (au premier démarrage)
if [ ! -f /var/www/html/.env ]; then
  cp /var/www/html/.env.example /var/www/html/.env
fi

# Génère la clé d'application Laravel si elle n'existe pas encore
if ! grep -q "^APP_KEY=base64" /var/www/html/.env; then
  php artisan key:generate --force
fi

php artisan config:cache
php artisan migrate --force

exec "$@"
