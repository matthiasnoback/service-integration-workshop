#!/usr/bin/env sh
sudo rabbitmqctl delete_vhost /
sudo rabbitmqctl add_vhost /
sudo rabbitmqctl set_permissions -p / user ".*" ".*" ".*"
