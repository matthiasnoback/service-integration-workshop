# Getting started

1. Clone this project. Make sure you have `docker` and `docker-compose` installed on your host machine.
2. Create an empty `.env` file in the root directory of the project.
3. Then, run:

    ```
    docker-compose pull
    ```

4. When the Docker images have been pulled, you should first install project dependencies using Composer:

    ```
    ./bin/composer.sh install
    ```

5. Follow the general advice from the [php-workshop-tools README](https://github.com/matthiasnoback/php-workshop-tools) about setting the correct environment variables and configuring PhpStorm. **Don't skip this step!**
6. To integrate with [Stripe](https://dashboard.stripe.com/test/dashboard) and [Pusher](https://dashboard.pusher.com/), create accounts for these services. Copy the contents of `.env.dist` to the `.env` you just created and paste your API keys in there. For Pusher, you should first create an "app".
7. Then you can finally run:
    
    ```
    docker-compose up -d
    ```

# Useful UIs

- The main website: [localhost:8080](http://localhost:8080).
- RabbitMQ Management UI: [localhost:15672](http://localhost:15672/). You can log in with username "user" and password "password".
- Redis browser: [localhost:8079](http://localhost:8079/)

# Tips

- Use [`docker-compose logs`](https://docs.docker.com/compose/reference/logs/) to find out what's going on in the containers. Add `-f` to follow the logs.
- Use [`docker-compose restart [container name]`](https://docs.docker.com/compose/reference/restart/) to restart a container (e.g. the `orders_and_registrations` container). This is particularly relevant when you want to test changes you made to the code of a RabbitMQ consumer.
- Run `./bin/composer.sh require ...` to install additional packages.
- Run `./bin/recreate.sh [container_name]` to recreate a container (if you provide no arguments, all containers will be recreated).
