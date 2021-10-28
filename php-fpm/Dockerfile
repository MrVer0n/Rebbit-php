FROM php:8-fpm

RUN apt-get update && apt-get install -y \
      zlib1g-dev libicu-dev librabbitmq-dev libzip-dev libonig-dev  g++ \
      git \
      mariadb-client \
      vim \
      curl \
      zip \
      && docker-php-ext-configure zip \
      && docker-php-ext-install zip \
      && docker-php-ext-install sysvsem \
      && docker-php-ext-configure intl \
      && docker-php-ext-install -j$(nproc) intl \
      && docker-php-ext-install -j$(nproc) pdo_mysql \
      && docker-php-ext-install -j$(nproc) mysqli \
      && docker-php-ext-configure opcache --enable-opcache \
      && docker-php-ext-install opcache \
      && pecl install amqp-1.11.0beta \
      && docker-php-ext-enable amqp

# Enable php8-xdebug if $PHP_XDEBUG_MODE is not empty
ARG PHP_XDEBUG_MODE=off
ARG PHP_XDEBUG_CLIENT_PORT=5902
ARG PHP_XDEBUG_CLIENT_HOST=host.docker.internal
COPY xdebug.ini  /etc/php8/conf.d/xdebug.ini.template
RUN if [[ "$PHP_XDEBUG_MODE" != "" ]]; then \
        pecl install xdebug; \
        docker-php-ext-enable xdebug; \
        export PHP_XDEBUG_MODE=$PHP_XDEBUG_MODE; \
        export PHP_XDEBUG_CLIENT_PORT=$PHP_XDEBUG_CLIENT_PORT; \
        export PHP_XDEBUG_CLIENT_HOST=$PHP_XDEBUG_CLIENT_HOST; \
        envsubst < /etc/php8/conf.d/xdebug.ini.template > /etc/php8/conf.d/xdebug.ini; \
    fi
RUN rm -f /etc/php8/conf.d/xdebug.ini.template

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY symfony.ini /etc/php8/conf.d/
COPY symfony.ini /etc/php8/cli/conf.d/

COPY symfony.pool.conf /etc/php8/php-fpm.d/

WORKDIR /var/www/symfony

CMD ["php-fpm", "-F"]
