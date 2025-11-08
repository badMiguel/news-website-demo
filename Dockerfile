FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    build-essential \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install mysqli pdo pdo_sqlite \
    && apt-get clean \
    && apt-get remove -y build-essential

WORKDIR /var/www/html
COPY . /var/www/html/

EXPOSE 9000

CMD [ "php-fpm" ]
