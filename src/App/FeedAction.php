<?php

declare(strict_types=1);

namespace KiH\App;

use KiH\Client;
use KiH\Generator;
use KiH\Parser;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class FeedAction
{
    /** @var Client */
    private $client;

    /** @var Parser */
    private $parser;

    /** @var Generator */
    private $generator;

    public function __construct(Client $client, Parser $parser, Generator $generator)
    {
        $this->client = $client;
        $this->parser = $parser;
        $this->generator = $generator;
    }

    public function __invoke(Request $request, Response $response)
    {
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
    }
}
