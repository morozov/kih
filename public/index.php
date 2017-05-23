<?php

declare(strict_types=1);

use KiH\Action\Feed;
use KiH\Action\Index;
use KiH\Action\Media;
use KiH\Middleware\BasePath;
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

$app = new App(array_merge(
    require __DIR__ . '/../etc/services.php',
    require __DIR__ . '/../etc/config.php'
));

$container = $app->getContainer();

$app->get('/', Index::class)
    ->setName('index');
$app->get('/rss.xml', Feed::class)
    ->setName('feed');
$app->get('/media/{id}.mp3', Media::class)
    ->setName('media');
$app->add($container->get(BasePath::class))
    ->run();
