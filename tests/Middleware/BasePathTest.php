<?php declare(strict_types=1);

namespace KiH\Tests\Middleware;

use KiH\Middleware\BasePath;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Uri;
use Slim\Router;
use stdClass;

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
            ->willReturn(Uri::createFromString('http://example.com/rss.xml'));
        $router     = $this->getRouter('http://example.com');
        $middleware = new BasePath($router, null);
        $this->assertMiddlewareWorks($middleware, $request);
    }

    private function getRouter(string $expected) : Router
    {
        $router = $this->createPartialMock(Router::class, ['setBasePath']);
        $router->expects($this->once())
            ->method('setBasePath')
            ->with($expected);

        return $router;
    }

    private function assertMiddlewareWorks(BasePath $middleware, Request $request) : void
    {
        $response     = $this->createMock(Response::class);
        $nextResponse = $this->createMock(Response::class);

        /** @var callable|MockObject $next */
        $next = $this->createPartialMock(stdClass::class, ['__invoke']);
        $next->expects($this->once())
            ->method('__invoke')
            ->with($request, $response)
            ->willReturn($nextResponse);

        $this->assertSame(
            $nextResponse,
            $middleware($request, $response, $next)
        );
    }
}
