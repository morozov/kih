<?php

declare(strict_types=1);

namespace KiH\Action;

use KiH\Client;
use KiH\Generator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Feed
{
    /** @var Client */
    private $client;

    /** @var Generator */
    private $generator;

    public function __construct(Client $client, Generator $generator)
    {
        $this->client = $client;
        $this->generator = $generator;
    }

    public function __invoke(Request $request, Response $response) : Response
    {
        $response = $response
            ->withHeader('Content-Type', 'text/xml; charset=UTF-8');

        $response->getBody()->write(
            $this->generator->generate(
                $this->client->getFeed()
            )->saveXML()
        );

        return $response;
    }
}
