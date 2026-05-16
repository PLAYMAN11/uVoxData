FROM php:8.2-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl unzip git nodejs npm \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist

COPY package.json package-lock.json* ./
RUN npm install

COPY . .

RUN composer dump-autoload

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]