<?php

declare(strict_types=1);

namespace KiH\Action;

use KiH\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use function assert;
use function is_string;

class Media
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $id = $request->getAttribute('id');
        assert(is_string($id));

        return $response->withHeader('Location', $this->client->getMedia($id)->url);
    }
}
