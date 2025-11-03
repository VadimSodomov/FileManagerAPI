#!/bin/bash
set -e

if [ ! -d config/jwt ]; then
    mkdir -p config/jwt
fi

if [ ! -d public/uploads ]; then
    mkdir -p public/uploads
    chown -R www-data:www-data public/uploads config/jwt
    chmod -R 775 public/uploads config/jwt
fi

if [ ! -f config/jwt/private.pem ] || [ ! -f config/jwt/public.pem ]; then
    echo "Генерация ключей для JWT..."
    openssl genpkey -algorithm RSA -out config/jwt/private.pem -aes256 -pass pass:${JWT_PASSPHRASE}
    openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:${JWT_PASSPHRASE}
    chmod 644 config/jwt/*.pem
fi

echo "Установка зависимостей Composer..."
composer install --ignore-platform-reqs --no-interaction

echo "Применение миграций..."
php bin/console doctrine:migrations:migrate --no-interaction

exec "$@"
