<?php

declare(strict_types=1);

namespace KiH\App;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class IndexAction
{
    /** @var string */
    private $baseUri;

    public function __construct(string $baseUri)
    {
        $this->baseUri = $baseUri;
    }

    public function __invoke(Request $request, Response $response)
    {
        return $response->withHeader('Location', $this->baseUri . '/rss.xml');
    }
}
