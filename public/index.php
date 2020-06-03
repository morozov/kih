<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Slim\App;

require __DIR__ . '/../vendor/autoload.php';

(static function (): void {
    $container = require __DIR__ . '/../etc/container.php';
    assert($container instanceof ContainerInterface);
    $container
        ->get(App::class)
        ->run();
})();
