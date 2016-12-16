FROM php:7.0-cli
COPY docker/devtools/script/* /usr/local/bin/
RUN chmod +x /usr/local/bin/*
RUN install-composer
WORKDIR /code
ENTRYPOINT ["docker-entrypoint.sh"]
