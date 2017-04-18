<?php

namespace KiH\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Router;

class BasePath
{
    /** @var Router */
    private $router;

    /** @var string */
    private $baseUri;

    public function __construct(Router $router, string $baseUri)
    {
        $this->router = $router;
        $this->baseUri = $baseUri;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $this->router->setBasePath($this->baseUri);

        return $next($request, $response);
    }
}
