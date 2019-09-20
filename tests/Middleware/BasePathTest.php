<?php declare(strict_types=1);

namespace KiH\Tests\Middleware;

use GuzzleHttp\Psr7\Uri;
use KiH\Middleware\BasePath;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Interfaces\RouteCollectorInterface;

class BasePathTest extends TestCase
{
    /**
     * @test
     */
    public function withBaseUri() : void
    {
        $request    = $this->createMock(Request::class);
        $baseUri    = 'http://example.com/api';
        $router     = $this->getRouter($baseUri);
        $middleware = new BasePath($router, $baseUri);
        $this->assertMiddlewareWorks($middleware, $request);
    }

    /**
     * @test
     */
    public function withoutBaseUri() : void
    {
        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getUri')
            ->willReturn(new Uri('http://example.com/rss.xml'));
        $router     = $this->getRouter('http://example.com');
        $middleware = new BasePath($router);
        $this->assertMiddlewareWorks($middleware, $request);
    }

    private function getRouter(string $expected) : RouteCollectorInterface
    {
        $router = $this->createMock(RouteCollectorInterface::class);
        $router->expects($this->once())
            ->method('setBasePath')
            ->with($expected);

        return $router;
    }

    private function assertMiddlewareWorks(BasePath $middleware, Request $request) : void
    {
        $response = $this->createMock(Response::class);

        /** @var RequestHandlerInterface|MockObject $next */
        $next = $this->createMock(RequestHandlerInterface::class);
        $next->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $this->assertSame(
            $response,
            $middleware($request, $next)
        );
    }
}
