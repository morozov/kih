<?php declare(strict_types=1);

namespace KiH\Action;

use DateTimeZone;
use KiH\Client;
use KiH\Generator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function assert;
use function is_string;

class Feed
{
    private Client $client;

    private Generator $generator;

    public function __construct(Client $client, Generator $generator)
    {
        $this->client    = $client;
        $this->generator = $generator;
    }

    public function __invoke(Request $request, Response $response) : Response
    {
        $response = $response
            ->withHeader('Content-Type', 'text/xml; charset=UTF-8');

        $feed = $this->client->getFeed();

        foreach ($feed as $item) {
            $response = $response
                ->withHeader(
                    'Expires',
                    $item->getCreatedAt()
                        ->setTimezone(new DateTimeZone('UTC'))
                        ->modify('+1 day')->format('D, d M Y H:i:s \G\M\T')
                );
            break;
        }

        $xml = $this->generator->generate($feed, $request->getUri())
            ->saveXML();
        assert(is_string($xml));

        $response->getBody()->write($xml);

        return $response;
    }
}
