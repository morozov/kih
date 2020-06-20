<?php declare(strict_types=1);

namespace KiH\Tests\Action;

use DateTimeImmutable;
use DateTimeZone;
use DOMDocument;
use GuzzleHttp\Psr7\Response;
use KiH\Action\Feed as Action;
use KiH\Client;
use KiH\Entity\Feed as Entity;
use KiH\Entity\Item;
use KiH\Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UriInterface;
use const PHP_EOL;

class FeedTest extends TestCase
{
    /**
     * @test
     */
    public function invoke() : void
    {
        $request = $this->createMock(Request::class);
        $request->method('getUri')
            ->willReturn($this->createMock(UriInterface::class));

        $entity   = new Entity([
            new Item(
                '',
                '',
                new DateTimeImmutable('2017-11-27 19:08:30', new DateTimeZone('Europe/Moscow')),
                '',
                0,
                '',
                ''
            ),
            new Item(
                '',
                '',
                new DateTimeImmutable('2017-11-23 19:10:20', new DateTimeZone('Europe/Moscow')),
                '',
                0,
                '',
                ''
            ),
        ]);
        $document = new DOMDocument();

        /** @var Client|MockObject $client */
        $client = $this->getMockBuilder(Client::class)
            ->onlyMethods(['getFeed'])
            ->getMockForAbstractClass();
        $client->expects(
            $this->once()
        )->method('getFeed')
            ->willReturn($entity);

        /** @var Generator|MockObject $generator */
        $generator = $this->getMockBuilder(Generator::class)
            ->onlyMethods(['generate'])
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
