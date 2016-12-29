# Getting started

Clone this project. Make sure you have `docker` and `docker-compose` installed on your host machine.

Define the following environment variables:

    # if not defined yet
    export COMPOSER_HOME=$HOME/.composer
    export HOST_UID=$(id -u)
    export HOST_GID=$(id -g)

Find out the IP address of your host machine, e.g. by running `ifconfig`, then export the host IP like this:

    export DOCKER_HOST_IP=192.168.1.47

Then run:

    docker-compose pull

When the Docker images have been pulled, you should first install project dependencies using Composer:

    ./composer.sh install

Then you can finally run:


    docker-compose up -d

Now go to [localhost:15672](http://localhost:15672/) in a browser. This will lead you to the RabbitMQ Management UI. You can log in with username "user" and password "password".

The website can be reached at [localhost](http://localhost).

# Tips

- Use [`docker-compose logs`](https://docs.docker.com/compose/reference/logs/) to find out what's going on in the containers.
- Use [`docker-compose restart orders_and_registrations`](https://docs.docker.com/compose/reference/restart/) to restart a container (in this case the `orders_and_registrations` container). This is particularly relevant when you want to test changes you made to the code of the RabbitMQ consumer.
- Run `./composer.sh require ...` to install additional packages.
