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

    /** @var string */
    private $redirectTo;

    public function __construct(RouterInterface $router, string $redirectTo)
    {
        $this->router = $router;
        $this->redirectTo = $redirectTo;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response) : Response
    {
        return $response->withHeader(
            'Location',
            $this->router->pathFor($this->redirectTo)
        );
    }
}
