FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates composer default-jre-headless fop git nginx postgresql-client unzip \
        php8.3-bcmath php8.3-cli php8.3-curl php8.3-fpm php8.3-gd php8.3-intl \
        php8.3-mbstring php8.3-pgsql php8.3-xml php8.3-zip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY . /app

RUN composer install --no-interaction --no-scripts --prefer-dist \
    && mkdir -p /run/php /var/www/akademos \
    && ln -s /app /var/www/akademos/current \
    && cp deploy/php-fpm/akademos.conf /etc/php/8.3/fpm/pool.d/akademos.conf \
    && cp deploy/nginx/akademos.conf /etc/nginx/sites-available/akademos \
    && ln -s /etc/nginx/sites-available/akademos /etc/nginx/sites-enabled/akademos

CMD ["bash", "deploy/validation/run.sh"]
