<?php

declare(strict_types=1);

use Slim\App;

require __DIR__ . '/../vendor/autoload.php';

(function () {
    /** @var \Psr\Container\ContainerInterface $container */
    $container = require __DIR__ . '/../etc/container.php';
    $container
        ->get(App::class)
        ->run();
})();
