# PHP-FPM for Laravel
FROM php:8.2-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    tzdata \
    && rm -rf /var/lib/apt/lists/*

# Set timezone
ENV TZ=Africa/Harare
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone \
    && echo "date.timezone = ${TZ}" > /usr/local/etc/php/conf.d/timezone.ini

# Increase upload limits for survey photos/attachments
RUN { \
      echo "upload_max_filesize=64M"; \
      echo "post_max_size=64M"; \
      echo "max_file_uploads=50"; \
      echo "max_execution_time=300"; \
      echo "max_input_time=300"; \
    } > /usr/local/etc/php/conf.d/uploads.ini

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql bcmath exif zip

# Optional: Redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Enable OPcache for performance
RUN docker-php-ext-install opcache
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Use tuned php-fpm pool configuration
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

# Copy application source code (ensure artisan exists before composer scripts)
COPY . ./

# Install PHP dependencies inside the image for baked-code workflow
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-interaction --no-progress --prefer-dist

CMD ["sh", "-lc", "mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache && chown -R www-data:www-data storage bootstrap/cache && chmod -R ug+rwX storage bootstrap/cache && exec php-fpm"]
