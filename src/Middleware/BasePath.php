<?php declare(strict_types=1);

namespace KiH\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Router;
use function rtrim;

class BasePath
{
    /** @var Router */
    private $router;

    /** @var string|null */
    private $baseUri;

    /**
     * Constructor
     *
     * @suppress PhanTypeMismatchProperty
     */
    public function __construct(Router $router, ?string $baseUri = null)
    {
        $this->router  = $router;
        $this->baseUri = $baseUri;
    }

    /**
     * Runs the middleware
     *
     * @suppress PhanTypeMismatchArgument
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        if ($this->baseUri !== null) {
            $basePath = $this->baseUri;
        } else {
            $basePath = rtrim((string) $request->getUri()->withPath('/'), '/');
        }

        $this->router->setBasePath($basePath);

        return $next($request, $response);
    }
}
