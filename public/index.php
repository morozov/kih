<?php

declare(strict_types=1);

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\App;

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
$container['client'] = function () {
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

$container['parser'] = function () {
    return new \KiH\Parser();
};

$container['generator'] = function () use ($baseUri) {
    return new \KiH\Generator(
        $baseUri,
        'Кремов и Хрусталёв',
        'http://www.radiorecord.ru/i/img/rr-logo-podcast.png'
    );
};

$app->get('/', function (Request $request, Response $response) use ($baseUri) {
    return $response->withHeader('Location', $baseUri . '/rss.xml');
});
$app->get('/rss.xml', function (Request $request, Response $response) {
    $rss = $this->generator->generate(
        $this->parser->parseFolder(
            (string) $this->client->getFolder()
        )
    );

    $response = $response
        ->withHeader('Content-Type', 'text/xml; charset=UTF-8');

    $response->getBody()->write(
        $rss->saveXML()
    );

    return $response;
});
$app->get('/media/{id}.mp3', function (Request $request, Response $response) {
    $item = $this->parser->parseItem(
        (string) $this->client->getItem(
            $request->getAttribute('id')
        )
    );

    return $response
        ->withHeader('Location', $item['@content.downloadUrl']);
});
$app->run();
