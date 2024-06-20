FROM php:8.2.19-zts-alpine3.20
RUN apk update && apk add --no-cache \
    openssl \
    zip \
    unzip \
    curl \
    nodejs \
    npm \
    openssh \
    bash
WORKDIR /app

COPY .env.example .env

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install
RUN composer require dompdf/dompdf
RUN composer require guzzlehttp/guzzle:^7.0
RUN composer require league/flysystem-read-only "^3.0"
EXPOSE 80
RUN php artisan key:generate
RUN php artisan migrate
CMD php artisan serve --host=0.0.0.0 --port=80
