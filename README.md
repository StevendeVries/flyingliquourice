[![Stories in Ready](https://badge.waffle.io/WeCamp/flyingliquourice.png?label=ready&title=Ready)](https://waffle.io/WeCamp/flyingliquourice)
[![Build Status](https://travis-ci.org/WeCamp/flyingliquourice.svg?branch=travis)](https://travis-ci.org/WeCamp/flyingliquourice)
[![StyleCI](https://styleci.io/repos/41369464/shield)](https://styleci.io/repos/41369464)

# FLYING LIQUOURICE

# Battleship

Made by:

* [Frank van den Brink](https://twitter.com/fvdb)
* [Randy Geraads](https://twitter.com/rgeraads)
* [Henri de Jong](https://github.com/aiolos)
* [Steven de Vries](https://twitter.com/Stedv)

## Setting up Automatically
* Make sure you have the following packages installed: Virtualbox, Vagrant, vagrant-hostsupdater and Ansible (ignore Ansible if you are on Windows)
* Run `vagrant up`

## Setting up Manually

* `composer install`
* `./vendor/bin/phinx migrate -e development`

## Running

* run `php ./bin/battleship.php [ip] [port]` ([ip] and [port] are optional and default to 127.0.0.1 and 1337)
* connect to your ip and port using telnet: `telnet [ip] [port]`

## Playing the game

### Battleship commands

Command | Description
------------ | -------------
`START [X:Y]` | Starts a game, optional give the X and Y size, defaults to 10x10
`RESUME <ID>` | Restart a game with the given ID
`STATUS` | Show the status of the game
`FIRE <X.Y>` | Fire on the given coordinates
`FIELD` | Show the current field with all shots on it
`SURRENDER` | Give up the game and lose

**Honourable mention to our WeCamp coach: [Eli White](https://twitter.com/EliW)**
