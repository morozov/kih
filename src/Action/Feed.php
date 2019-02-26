<?php declare(strict_types=1);

namespace KiH\Action;

use DateTimeZone;
use KiH\Client;
use KiH\Generator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Feed
{
    /** @var Client */
    private $client;

    /** @var Generator */
    private $generator;

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
            $date = clone $item->getCreatedAt();
            $date->setTimezone(new DateTimeZone('UTC'));
            $date->modify('+1 day');

            $response = $response
                ->withHeader('Expires', $date->format('D, d M Y H:i:s \G\M\T'));
            break;
        }

        $response->getBody()->write(
            $this->generator->generate($feed)->saveXML()
        );

        return $response;
    }
}
