# Getting started

Clone this project. Make sure you have `docker` and `docker-compose` installed on your host machine.

Run:

    docker-compose pull

When the Docker images have been pulled, you should first install project dependencies using Composer:

    ./composer.sh install

Follow the general advice from the [php-workshop-tools README](https://github.com/matthiasnoback/php-workshop-tools) about setting the correct environment variables and configuring PhpStorm. **Don't skip this step!**

Then you can finally run:

    docker-compose up -d

# Useful UIs

- The main website: [localhost:8080](http://localhost:8080).
- RabbitMQ Management UI: [localhost:15672](http://localhost:15672/). You can log in with username "user" and password "password".
- Redis browser: [localhost:8079](http://localhost:8079/)

# Tips

- Use [`docker-compose logs`](https://docs.docker.com/compose/reference/logs/) to find out what's going on in the containers. Add `-f` to follow the logs.
- Use [`docker-compose restart [container name]`](https://docs.docker.com/compose/reference/restart/) to restart a container (e.g. the `orders_and_registrations` container). This is particularly relevant when you want to test changes you made to the code of a RabbitMQ consumer.
- Run `./composer.sh require ...` to install additional packages.
