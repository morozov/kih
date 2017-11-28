<?php

declare(strict_types=1);

namespace KiH\Tests\Action;

use DateTime;
use DateTimeZone;
use DOMDocument;
use KiH\Action\Feed as Action;
use KiH\Client;
use KiH\Entity\Feed as Entity;
use KiH\Entity\Item;
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

        $entity = new Entity([
            new Item(
                '',
                '',
                new DateTime('2017-11-27 19:08:30', new DateTimeZone('Europe/Moscow')),
                '',
                0,
                '',
                ''
            ),
            new Item(
                '',
                '',
                new DateTime('2017-11-23 19:10:20', new DateTimeZone('Europe/Moscow')),
                '',
                0,
                '',
                ''
            ),
        ]);
        $document = new DOMDocument();

        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder(Client::class)
            ->setMethods(['getFeed'])
            ->getMockForAbstractClass();
        $client->expects(
            $this->once()
        )->method('getFeed')
            ->willReturn($entity);

        /** @var Generator|\PHPUnit_Framework_MockObject_MockObject $generator */
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
            'Tue, 28 Nov 2017 16:08:30 GMT',
            $response->getHeaderLine('Expires')
        );

        $this->assertEquals(
            '<?xml version="1.0"?>' . PHP_EOL,
            (string) $response->getBody()
        );
    }
}
