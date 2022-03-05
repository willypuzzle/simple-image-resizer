FROM php:8.0-cli

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer


RUN apt-get update
RUN apt-get install -y libzip-dev libpng-dev libonig-dev libsqlite3-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo zip gd mbstring exif pdo_sqlite

COPY . /

RUN cp .env.example .env

RUN mkdir /bootstrap/cache
RUN chmod -R 777 /bootstrap/cache
RUN chmod -R 777 /storage
RUN cd / && composer install
RUN php artisan key:generate
RUN php artisan migrate

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
