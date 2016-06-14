#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../data/ships.php';
$loop   = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$conns  = new \SplObjectStorage();

$ip   = array_key_exists(1, $argv) ? $argv[1] : '127.0.0.1';
$port = array_key_exists(2, $argv) ? $argv[2] : 1337;

$dbh        = new \PDO('sqlite:' . __DIR__ . '/../data/games');
$repository = new \Wecamp\FlyingLiqourice\Storage\SqliteGameRepository($dbh);

$socket->on('connection', function ($conn) use ($conns, $repository) {
    $conn->id = '';
    $conns->attach($conn);
    $conn->write(PHP_EOL . PHP_EOL);
    $conn->write(showShip() . PHP_EOL);
    $conn->write('WELCOME TO BATTLESHIP' . PHP_EOL);
    $conn->write(PHP_EOL . PHP_EOL);
    $conn->write('Use `help` to see all available commands' . PHP_EOL);

    $conn->on('data', function ($data) use ($conns, $conn, $repository) {
        foreach ($conns as $current) {
            if ($conn === $current) {
                $service = new \Wecamp\FlyingLiqourice\Service\ServiceListener($data, $conn->id, $repository);
                try {
                    $current->write($service->run() . PHP_EOL);
                    $conn->id = (string) $service->id();
                } catch (\InvalidArgumentException $e) {
                    $current->write('ERROR ' . get_class($e) . PHP_EOL);
                }
            }
        }
    });
    $conn->on('end', function () use ($conns, $conn) {
        $conns->detach($conn);
    });
});

try {
    $socket->listen($port, $ip);
    echo 'Socket server listening on port ' . $port . ".\n";
    echo 'You can connect to it by running: telnet ' . $ip . ' ' . $port . "\n";
    $loop->run();
} catch (exception $e) {
    echo 'Daemon: ',  $e->getMessage(), "\n";
}
