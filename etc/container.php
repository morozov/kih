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
use Slim\Container;

return new Container(array_merge([
    Client::class => static function (Container $container) : Client {
        $settings = $container->get('settings')['vk'];

        return new VkClient(
            new HttpClient(),
            $settings['group'],
            $settings['access_token']
        );
    },
    Generator::class => static function (Container $container) : Generator {
        return new Rss(
            $container->get('router'),
            $container->get('settings')['feed']
        );
    },
    Index::class => static function (Container $container) : Index {
        return new Index(
            $container->get('router'),
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
    BasePath::class => static function (Container $container) : BasePath {
        return new BasePath(
            $container->get('router'),
            $container->get('settings')['baseUri']
        );
    },
    App::class => static function (Container $container) : App {
        $app = new App($container);
        $app->get('/', Index::class)
            ->setName('index');
        $app->get('/rss.xml', Feed::class)
            ->setName('feed');
        $app->get('/media/{id}.mp3', Media::class)
            ->setName('media');
        $app->add($container->get(BasePath::class));

        return $app;
    },
], require __DIR__ . '/../etc/config.php'));
