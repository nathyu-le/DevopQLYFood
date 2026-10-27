FROM php:8.2-apache-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring \
    && apt-mark manual libonig5 \
    && apt-get purge -y --auto-remove libonig-dev \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/application.ini
COPY app/ app/
COPY public/ public/
COPY config/config.example.php config/config.example.php
COPY docker/entrypoint.php /usr/local/bin/configure-container.php
COPY docker/entrypoint.sh /usr/local/bin/application-entrypoint
RUN chmod 755 /usr/local/bin/application-entrypoint

ARG APP_REVISION=local
RUN printf '%s\n' "$APP_REVISION" > REVISION
LABEL org.opencontainers.image.source="https://github.com/nathyu-le/DevopQLYFood" \
      org.opencontainers.image.revision="$APP_REVISION"

HEALTHCHECK --interval=10s --timeout=5s --start-period=20s --retries=6 \
    CMD php -r '$r=@file_get_contents("http://127.0.0.1/health.php"); exit($r!==false && (json_decode($r,true)["status"]??null)==="ok" ? 0 : 1);'

ENTRYPOINT ["application-entrypoint"]
CMD ["apache2-foreground"]
