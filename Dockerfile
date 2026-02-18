FROM php:8.2-cli

WORKDIR /var/www

# ติดตั้ง dependency ที่ Laravel ต้องใช้
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# ติดตั้ง Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy project
COPY . .

# ติดตั้ง package
RUN composer install --no-dev --optimize-autoloader

EXPOSE 10000

CMD sh -c "sleep 10 && \
    php artisan migrate --force && \
    php artisan db:seed --force || true && \
    php -S 0.0.0.0:${PORT} -t public"





