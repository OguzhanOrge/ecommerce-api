FROM php:8.2-fpm

# Sistem paketleri
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Composer yükle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Çalışma dizini
WORKDIR /var/www

# Laravel dosyalarını kopyala
COPY . .

# İzinler
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage


CMD ["php-fpm"]
