<?php declare(strict_types=1);

namespace KiH\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Interfaces\RouteCollectorInterface;
use function rtrim;

class BasePath
{
    /** @var RouteCollectorInterface */
    private $routeCollector;

    /** @var string */
    private $baseUri;

    /**
     * Constructor
     *
     * @suppress PhanTypeMismatchProperty
     */
    public function __construct(RouteCollectorInterface $routeCollector, string $baseUri = '')
    {
        $this->routeCollector = $routeCollector;
        $this->baseUri        = $baseUri;
    }

    /**
     * Runs the middleware
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $next) : ResponseInterface
    {
        if ($this->baseUri !== '') {
            $basePath = $this->baseUri;
        } else {
            $basePath = (string) $request->getUri()->withPath('/');
        }

        $this->routeCollector->setBasePath(
            rtrim($basePath, '/')
        );

        return $next->handle($request);
    }
}
