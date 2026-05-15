FROM php:8.2-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl unzip git nodejs npm \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar dependencias PHP
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Instalar dependencias JS
COPY package.json package-lock.json* ./
RUN npm install

# Copiar el resto del código
COPY . .

RUN composer dump-autoload --optimize
RUN npm run build

# Generar APP_KEY si no existe ..env
RUN php artisan key:generate --force 2>/dev/null || true

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
