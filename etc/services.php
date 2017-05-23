<?php

use GuzzleHttp\Client as HttpClient;
use KiH\Action\Feed;
use KiH\Action\Index;
use KiH\Action\Media;
use KiH\Client;
use KiH\Client\OneDrive;
use KiH\Generator;
use KiH\Generator\Rss;
use KiH\Middleware\BasePath;
use Psr\Container\ContainerInterface as Container;

return [
    Client::class => function (Container $container) : Client {
        return new OneDrive(
            new HttpClient(),
            $container->get('settings')['share']
        );
    },
    Generator::class => function (Container $container) : Generator {
        return new Rss(
            $container->get('router'),
            $container->get('settings')['feed']
        );
    },
    Index::class => function (Container $container) : Index {
        return new Index(
            $container->get('router'),
            'feed'
        );
    },
    Feed::class => function (Container $container) : Feed {
        return new Feed(
            $container->get(Client::class),
            $container->get(Generator::class)
        );
    },
    Media::class => function (Container $container) : Media {
        return new Media(
            $container->get(Client::class)
        );
    },
    BasePath::class => function (Container $container) : BasePath {
        return new BasePath(
            $container->get('router'),
            $container->get('settings')['baseUri']
        );
    },
];
