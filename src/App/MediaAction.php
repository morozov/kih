<?php

declare(strict_types=1);

namespace KiH\App;

use KiH\Client;
use KiH\Parser;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class MediaAction
{
    /** @var Client */
    private $client;

    /** @var Parser */
    private $parser;

    public function __construct(Client $client, Parser $parser)
    {
        $this->client = $client;
        $this->parser = $parser;
    }

    public function __invoke(Request $request, Response $response)
    {
        $item = $this->parser->parseItem(
            (string) $this->client->getItem(
                $request->getAttribute('id')
            )
        );

        return $response->withHeader('Location', $item['@content.downloadUrl']);
    }
}
