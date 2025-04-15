FROM php:8.2-cli

WORKDIR /app

COPY . /app

RUN chown -R www-data:www-data /app
