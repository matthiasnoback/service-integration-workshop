FROM php:7.0-cli
COPY docker/base/config/php/app.ini ${PHP_INI_DIR}/conf.d/20-app.ini
COPY docker/base/script/* /usr/local/bin/
RUN docker-php-ext-install bcmath
RUN chmod +x /usr/local/bin/*
RUN install-xdebug
RUN docker-php-ext-install pcntl
ENTRYPOINT ["/bin/true"]
