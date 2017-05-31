<?php

namespace KiH\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Router;

class BasePath
{
    /** @var Router */
    private $router;

    /** @var string|null */
    private $baseUri;

    public function __construct(Router $router, ?string $baseUri)
    {
        $this->router = $router;
        $this->baseUri = $baseUri;
    }

    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        if ($this->baseUri !== null) {
            $basePath = $this->baseUri;
        } else {
            $basePath = rtrim($request->getUri()->withPath('/'), '/');
        }

        $this->router->setBasePath($basePath);

        return $next($request, $response);
    }
}
