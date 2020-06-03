<?php

declare(strict_types=1);

namespace KiH\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteParserInterface;

class Index
{
    private RouteParserInterface $routeParser;

    private string $redirectTo;

    public function __construct(RouteParserInterface $routeParser, string $redirectTo)
    {
        $this->routeParser = $routeParser;
        $this->redirectTo  = $redirectTo;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        return $response->withHeader(
            'Location',
            $this->routeParser->urlFor($this->redirectTo)
        );
    }
}
