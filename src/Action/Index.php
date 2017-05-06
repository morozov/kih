<?php

declare(strict_types=1);

namespace KiH\Action;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Interfaces\RouterInterface;

class Index
{
    /** @var RouterInterface */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function __invoke(Request $request, Response $response)
    {
        return $response->withHeader(
            'Location',
            $this->router->pathFor('feed')
        );
    }
}
