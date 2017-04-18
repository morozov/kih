<?php

declare(strict_types=1);

use KiH\Client;
use KiH\Parser;
use KiH\Generator;
use KiH\App\FeedAction;
use KiH\App\IndexAction;
use KiH\App\MediaAction;
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
    return new \KiH\Client(
        new \GuzzleHttp\Client(),
        $container->get('settings')['share']
    );
};

$container[Parser::class] = function () {
    return new Parser();
};

$container[Generator::class] = function (Container $container) {
    return new Generator(
        $container->get('router'),
        $container->get('settings')['feed']
    );
};

$container[IndexAction::class] = function (Container $container) {
    return new IndexAction(
        $container->get('router')
    );
};

$container[FeedAction::class] = function (Container $container) {
    return new FeedAction(
        $container->get(Client::class),
        $container->get(Parser::class),
        $container->get(Generator::class)
    );
};

$container[MediaAction::class] = function (Container $container) {
    return new MediaAction(
        $container->get(Client::class),
        $container->get(Parser::class)
    );
};

$container[BasePath::class] = function (Container $container) {
    return new BasePath(
        $container->get('router'),
        $container->get('settings')['baseUri']
    );
};

$app->get('/', IndexAction::class)
    ->setName('index');
$app->get('/rss.xml', FeedAction::class)
    ->setName('feed');
$app->get('/media/{id}.mp3', MediaAction::class)
    ->setName('media');
$app->add($container->get(BasePath::class))
    ->run();
