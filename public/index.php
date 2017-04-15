<?php

declare(strict_types=1);

use KiH\Client;
use KiH\Parser;
use KiH\Generator;
use KiH\App\FeedAction;
use KiH\App\IndexAction;
use KiH\App\MediaAction;
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

$app = new App([
    'settings' => [
        'displayErrorDetails' => true,
    ],
]);

$baseUri = 'https://s11v.tk/kih';

$container = $app->getContainer();

$container[Client::class] = function () {
    return new \KiH\Client(
        new \GuzzleHttp\Client(),
        'https://onedrive.live.com/redir.aspx?' . http_build_query([
            'cid' => '0b6c46ff0a72f8db',
            'resid' => 'B6C46FF0A72F8DB!196510',
            'parId' => 'B6C46FF0A72F8DB!116',
            'authkey' => '!AB17lqCz5De3HEE',
        ])
    );
};

$container[Parser::class] = function () {
    return new Parser();
};

$container[Generator::class] = function () use ($baseUri) {
    return new Generator(
        $baseUri,
        'Кремов и Хрусталёв',
        'http://www.radiorecord.ru/i/img/rr-logo-podcast.png'
    );
};

$container[IndexAction::class] = function () use ($baseUri) {
    return new IndexAction($baseUri);
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

$app->get('/', IndexAction::class);
$app->get('/rss.xml', FeedAction::class);
$app->get('/media/{id}.mp3', MediaAction::class);
$app->run();
