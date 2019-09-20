<?php declare(strict_types=1);

use GuzzleHttp\Client as HttpClient;
use KiH\Action\Feed;
use KiH\Action\Index;
use KiH\Action\Media;
use KiH\Client;
use KiH\Generator;
use KiH\Generator\Rss;
use KiH\Middleware\BasePath;
use KiH\Providers\Vk\Client as VkClient;
use Slim\App;
use Slim\Psr7\Factory\ResponseFactory;
use UltraLite\Container\Container;

$settings = require __DIR__ . '/../etc/config.php';

return new Container([
    Client::class => static function () use ($settings) : Client {
        $settings = $settings['vk'];

        return new VkClient(
            new HttpClient(),
            $settings['group'],
            $settings['access_token']
        );
    },
    Generator::class => static function (Container $container) use ($settings) : Generator {
        $app = $container->get(App::class);

        return new Rss(
            $app->getRouteCollector()->getRouteParser(),
            $settings['feed']
        );
    },
    Index::class => static function (Container $container) : Index {
        $app = $container->get(App::class);

        return new Index(
            $app->getRouteCollector()->getRouteParser(),
            'feed'
        );
    },
    Feed::class => static function (Container $container) : Feed {
        return new Feed(
            $container->get(Client::class),
            $container->get(Generator::class)
        );
    },
    Media::class => static function (Container $container) : Media {
        return new Media(
            $container->get(Client::class)
        );
    },
    App::class => static function (Container $container) use ($settings) : App {
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

        $app->add(new BasePath(
            $app->getRouteCollector(),
            $settings['baseUri']
        ));

        return $app;
    },
]);
