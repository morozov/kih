<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Slim\App;

if (PHP_SAPI === 'cli-server') {
    $info = parse_url($_SERVER['REQUEST_URI']);

    if (file_exists(__DIR__ . $info['path'])) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

(static function (): void {
    $container = require __DIR__ . '/../etc/container.php';
    assert($container instanceof ContainerInterface);
    $container
        ->get(App::class)
        ->run();
})();
