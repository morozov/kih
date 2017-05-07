<?php

declare(strict_types=1);

namespace KiH\Tests;

use DOMDocument;
use KiH\Action\Feed as Action;
use KiH\Client;
use KiH\Entity\Folder as Entity;
use KiH\Generator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;

class FeedTest extends TestCase
{
    /**
     * @test
     */
    public function invoke()
    {
        $request = $this->createMock(Request::class);

        $entity = new Entity([]);
        $document = new DOMDocument();

        $client = $this->getMockBuilder(Client::class)
            ->setMethods(['getFolder'])
            ->getMockForAbstractClass();
        $client->expects(
            $this->once()
        )->method('getFolder')
            ->willReturn($entity);

        $generator = $this->getMockBuilder(Generator::class)
            ->setMethods(['generate'])
            ->getMockForAbstractClass();
        $generator->expects(
            $this->once()
        )->method('generate')
            ->willReturn($document);

        $action = new Action($client, $generator);

        $response = $action($request, new Response());

        $this->assertEquals(
            'text/xml; charset=UTF-8',
            $response->getHeaderLine('Content-Type')
        );

        $this->assertEquals(
            '<?xml version="1.0"?>' . PHP_EOL,
            (string) $response->getBody()
        );
    }
}
