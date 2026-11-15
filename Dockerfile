# ==========================================
# Stage 1: Build Dependencies via Composer
# ==========================================
FROM composer:2 AS vendor-builder

WORKDIR /app

# Copy composer files first to leverage Docker layer caching
COPY composer.json composer.lock ./

# Install production dependencies only (no dev packages like sail, pest, mockery)
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# ==========================================
# Stage 2: Production Lightweight Image
# ==========================================
FROM php:8.4-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies & build tools in a single layer, then remove build deps
RUN apk update && apk add --no-cache \
    nginx \
    curl \
    ca-certificates \
    libpq \
    libzip \
    libpng \
    icu-libs \
    && apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    postgresql-dev \
    libzip-dev \
    libpng-dev \
    icu-dev \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_pgsql \
    bcmath \
    opcache \
    zip \
    gd \
    intl \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/*

# Copy low-memory PHP and Nginx configs
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy vendor from builder stage
COPY --from=vendor-builder /app/vendor /var/www/html/vendor

# Copy application source code
COPY . /var/www/html

# Generate authoritative classmap and package discovery
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev 2>/dev/null || true

# Set proper ownership and permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Nginx listens on 8000 inside the container (the reverse proxy forwards to it)
EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
