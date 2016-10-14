# Getting started

Clone this project and run `vagrant up` in the root directory. This will set up a virtual machine, with PHP and RabbitMQ.

Afterwards, run:

    vagrant ssh
    cd /vagrant
    composer install
    vendor/bin/phpunit

You should see a green bar, indicating that the tests pass.

Go to [http://192.168.33.99:15672](http://192.168.33.99:15672/) in a browser, and log in with username "user" and password "password". This will lead you to the RabbitMQ Management UI.
