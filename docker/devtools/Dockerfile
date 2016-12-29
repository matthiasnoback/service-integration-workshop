FROM ibuildingsnl/rabbitmq_workshop_base
COPY docker/devtools/script/* /usr/local/bin/
RUN chmod +x /usr/local/bin/*
RUN install-composer
WORKDIR /code
ENTRYPOINT ["docker-entrypoint.sh"]
