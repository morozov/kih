<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use GuzzleHttp\Client as HttpClient;
use KiH\Action\Feed;
use KiH\Action\Index;
use KiH\Action\Media;
use KiH\Client;
use KiH\Generator;
use KiH\Generator\Rss;
use KiH\Providers\Vk\Client as VkClient;
use Slim\App;
use Slim\Psr7\Factory\ResponseFactory;
use UltraLite\Container\Container;

if (class_exists(Dotenv::class)) {
    Dotenv::createImmutable(dirname(__DIR__))
        ->safeLoad();
}

return new Container([
    Client::class => static function (): Client {
        return new VkClient(
            new HttpClient(),
            'kremhrust',
            $_ENV['VK_ACCESS_TOKEN'] ?? ''
        );
    },
    Generator::class => static function (Container $container): Generator {
        $app = $container->get(App::class);

        return new Rss(
            $app->getRouteCollector()->getRouteParser(),
            'Кремов и Хрусталёв',
            'http://www.radiorecord.ru/i/img/rr-logo-podcast.png'
        );
    },
    Index::class => static function (Container $container): Index {
        $app = $container->get(App::class);

        return new Index(
            $app->getRouteCollector()->getRouteParser(),
            'feed'
        );
    },
    Feed::class => static function (Container $container): Feed {
        return new Feed(
            $container->get(Client::class),
            $container->get(Generator::class)
        );
    },
    Media::class => static function (Container $container): Media {
        return new Media(
            $container->get(Client::class)
        );
    },
    App::class => static function (Container $container): App {
        $_SERVER['HTTPS'] = true;

        $app = new App(
            new ResponseFactory(),
            $container
        );
        $app->get('/', Index::class)
            ->setName('index');
        $app->get('/rss.xml', Feed::class)
            ->setName('feed');
        $app->get('/media/{id}.mp3', Media::class)
            ->setName('media');

        return $app;
    },
]);
