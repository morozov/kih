<?php

declare(strict_types=1);

namespace KiH\Action;

use KiH\Client;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Media
{
    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function __invoke(Request $request, Response $response)
    {
        $item = $this->client->getItem(
            $request->getAttribute('id')
        );

        return $response->withHeader('Location', $item['@content.downloadUrl']);
    }
}
