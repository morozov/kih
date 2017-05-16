<?php

declare(strict_types=1);

namespace KiH\Tests\Generator;

use DateTime;
use KiH\Entity\File;
use KiH\Entity\Folder;
use KiH\Generator\Rss;
use PHPUnit\Framework\TestCase;
use Slim\Interfaces\RouterInterface;

class RssTest extends TestCase
{
    /**
     * @test
     */
    public function generate()
    {
        /** @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject $router */
        $router = $this->getMockBuilder(RouterInterface::class)
            ->setMethods(['pathFor'])
            ->getMockForAbstractClass();
        $router->expects(
            $this->any()
        )->method('pathFor')
            ->willReturnCallback(function (string $name, array $params) {
                return 'http://example.com/index.php?'
                    . http_build_query(array_merge(['page' => $name], $params));
            });

        $folder = new Folder([
            new File(
                'B6C46FF0A72F8DB!703491',
                'Test Episode',
                new DateTime('2017-05-05T19:15:33.14Z'),
                'https://1drv.ms/u/s!Atv4cgr_RmwLqvgDHXuWoLPkN7ccQQ',
                3379032,
                'audio/mpeg'
            )]
        );

        $rss = new Rss($router, [
            'title' => 'Test Title',
            'logo' => 'http://example.com/logo.png',
        ]);

        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/fixtures/rss.xml',
            $rss->generate($folder)
        );
    }
}
