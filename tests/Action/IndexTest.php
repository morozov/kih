<?php

declare(strict_types=1);

namespace KiH\Tests\Action;

use KiH\Action\Index;
use PHPUnit\Framework\MockObject\MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;
use Slim\Interfaces\RouterInterface;

class IndexTest extends TestCase
{
    /**
     * @test
     */
    public function invoke()
    {
        /** @var RouterInterface|MockObject $router */
        $router = $this->createMock(RouterInterface::class);
        $router->expects(
            $this->once()
        )
            ->method('pathFor')
            ->with('route-name')
            ->willReturn('/new/location');

        $action = new Index($router, 'route-name');

        $request = $this->createMock(Request::class);
        $response = $action($request, new Response());

        $this->assertEquals('/new/location', $response->getHeaderLine('Location'));
    }
}
