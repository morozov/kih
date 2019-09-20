<?php declare(strict_types=1);

namespace KiH\Tests\Action;

use GuzzleHttp\Psr7\Response;
use KiH\Action\Index;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteParserInterface;

class IndexTest extends TestCase
{
    /**
     * @test
     */
    public function invoke() : void
    {
        /** @var RouteParserInterface|MockObject $router */
        $router = $this->createMock(RouteParserInterface::class);
        $router->expects(
            $this->once()
        )
            ->method('urlFor')
            ->with('route-name')
            ->willReturn('/new/location');

        $action = new Index($router, 'route-name');

        $request  = $this->createMock(Request::class);
        $response = $action($request, new Response());

        $this->assertEquals('/new/location', $response->getHeaderLine('Location'));
    }
}
