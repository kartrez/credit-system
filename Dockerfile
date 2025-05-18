FROM php:8.3-fpm

# Установка основных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    intl \
    opcache

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www/credit-system

# Копирование файлов проекта
COPY . /var/www/credit-system/

# Установка зависимостей
RUN composer install --no-scripts --no-interaction --optimize-autoloader

# PHP конфигурация для разработки
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Разрешения
RUN chmod +x bin/console
RUN chown -R www-data:www-data /var/www/credit-system

EXPOSE 9000

CMD ["php-fpm"] 