<?php declare(strict_types=1);

namespace KiH\Action;

use KiH\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Media
{
    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function __invoke(Request $request, Response $response) : Response
    {
        return $response->withHeader('Location', $this->client->getMedia(
            $request->getAttribute('id')
        )->getUrl());
    }
}
