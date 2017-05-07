<?php

declare(strict_types=1);

use KiH\Action\Feed;
use KiH\Action\Index;
use KiH\Action\Media;
use KiH\Client;
use KiH\Generator;
use KiH\Middleware\BasePath;
use Psr\Container\ContainerInterface as Container;
use Slim\App;

if (php_sapi_name() == 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if ($path !== '/') {
        $path = __DIR__ . $path;

        if ($path !== __FILE__ && file_exists($path)) {
            return false;
        }
    }

    // @link http://stackoverflow.com/questions/24336725/slim-framework-cannot-interpret-routes-with-dot
    $_SERVER['SCRIPT_NAME'] = basename(__FILE__);
}

require __DIR__ . '/../vendor/autoload.php';

$app = new App(require __DIR__ . '/../etc/config.php');

$container = $app->getContainer();

$container[Client::class] = function (Container $container) {
    return new \KiH\Client\OneDrive(
        new \GuzzleHttp\Client(),
        $container->get('settings')['share']
    );
};

$container[Generator::class] = function (Container $container) {
    return new Generator(
        $container->get('router'),
        $container->get('settings')['feed']
    );
};

$container[Index::class] = function (Container $container) {
    return new Index(
        $container->get('router'),
        'feed'
    );
};

$container[Feed::class] = function (Container $container) {
    return new Feed(
        $container->get(Client::class),
        $container->get(Generator::class)
    );
};

$container[Media::class] = function (Container $container) {
    return new Media(
        $container->get(Client::class)
    );
};

$container[BasePath::class] = function (Container $container) {
    return new BasePath(
        $container->get('router'),
        $container->get('settings')['baseUri']
    );
};

$app->get('/', Index::class)
    ->setName('index');
$app->get('/rss.xml', Feed::class)
    ->setName('feed');
$app->get('/media/{id}.mp3', Media::class)
    ->setName('media');
$app->add($container->get(BasePath::class))
    ->run();
