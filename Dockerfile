# ============================================================================
#  Records Management / Document Tracking System  -  production image
#  One container runs nginx + PHP-FPM (managed by supervisord).
#  The database is a separate container (see docker-compose.yml).
#
#  PHP 8.5.12 is required: the locked Symfony packages need PHP >= 8.4.1.
#  No Node/Vite build is needed: the pages load their CSS/JS from CDNs.
# ============================================================================

# ---- Stage 1: PHP with the extensions the app needs --------------------------
FROM php:8.5.12-fpm-alpine AS base

# pdo_mysql -> talk to MariaDB, opcache -> faster PHP
RUN docker-php-ext-install -j"$(nproc)" pdo_mysql


# ---- Stage 2: install PHP dependencies (Composer) ----------------------------
FROM base AS vendor

# the build runs as root; tell Composer that is expected
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apk add --no-cache unzip git
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Dependencies first, so Docker can reuse this layer when only app code changes
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress \
        --no-scripts --no-autoloader

# Then the application code
COPY . .
RUN mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions \
             storage/framework/views storage/logs bootstrap/cache \
 && composer dump-autoload --no-dev --optimize --no-interaction


# ---- Stage 3: final runtime image --------------------------------------------
FROM base AS final

RUN apk add --no-cache nginx supervisor su-exec curl tzdata \
 && mkdir -p /run/nginx \
 && ln -sf /dev/stdout /var/log/nginx/access.log \
 && ln -sf /dev/stderr /var/log/nginx/error.log

# nginx / PHP / supervisor settings
COPY docker/nginx.conf       /etc/nginx/http.d/default.conf
COPY docker/php.ini          /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php-fpm.conf     /usr/local/etc/php-fpm.d/zz-app.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh    /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/html
COPY --from=vendor --chown=www-data:www-data /app /var/www/html

# Files uploaded through a NAS (File Station, ZIP) can arrive with odd permission bits.
# Normalise them: folders 755, files 644, and the cache folder writable for the web user.
RUN find /var/www/html -type d -exec chmod 755 {} + \
 && find /var/www/html -type f -exec chmod 644 {} + \
 && chmod -R u+rwX,g+rwX /var/www/html/bootstrap/cache

EXPOSE 80

# Laravel's built-in health route (does not touch the database)
HEALTHCHECK --interval=30s --timeout=5s --start-period=90s --retries=3 \
    CMD curl -fsS http://127.0.0.1/up || exit 1

ENTRYPOINT ["entrypoint"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
