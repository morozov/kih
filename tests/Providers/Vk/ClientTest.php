<?php

declare(strict_types=1);

namespace KiH\Tests\Providers\Vk;

use DateTime;
use GuzzleHttp\Client as HttpClient;
use KiH\Providers\Vk\Client;
use KiH\Entity\Feed;
use KiH\Entity\Item;
use KiH\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class ClientTest extends TestCase
{
    /**
     * @test
     */
    public function getFeed()
    {
// @codingStandardsIgnoreStart
        $httpClient = $this->createHttpClientMock(
            'GET',
            'https://api.vk.com/method/wall.search?domain=kremhrust&query=%D0%90%D1%83%D0%B4%D0%B8%D0%BE%D0%B7%D0%B0%D0%BF%D0%B8%D1%81%D1%8C+%D1%8D%D1%84%D0%B8%D1%80%D0%B0&owners_only=1&count=10&access_token=the-token&v=5.68',
            $this->getFixture('success/feed.json')
        );

        $this->assertEquals(new Feed([
            new Item(
                'https://cs9-18v4.userapi.com/p7/4eff52f01a3876.mp3?extra=D1RrASTFkGtw1iSphK8_p3hWuClIPWH4m5r0HQPTzp-pFIdHoL2z1Xec0iJE3LE_Qkyvy5Xf7knrimelZjopt9bGdIYh2p40Yz1uWsGxImo3cKL7imYtIdFwJtmAILhTEjE7ORQEFELcoYA',
                'Эфир от 21 сентября 2017',
                new DateTime('2017-09-21T19:14:00.000000+0000'),
                '308269',
                3500,
                'audio/mpeg',
            <<<EOF
Аудиозапись эфира от 21 сентября 2017 (четверг) 
 
Архив аудиозаписей с возможностью загрузки: https://vk.cc/6X3JkI
EOF
            )
        ]), (new Client($httpClient, 'kremhrust', 'the-token'))->getFeed());
// @codingStandardsIgnoreEnd
    }

    /**
     * @test
     * @dataProvider failureProvider
     */
    public function parseFailure(string $fixture)
    {
        /** @var HttpClient|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClient::class);
        $mocker = $httpClient->expects(
            $this->once()
        )->method('request');

        $this->expectResponse($mocker, $this->getFixture('failure/folder/' . $fixture));

        $this->expectException(Exception::class);
        (new Client($httpClient, 'kremhrust', 'the-token'))->getFeed();
    }

    public static function failureProvider()
    {
        return [
            'invalid-syntax' => [
                'invalid-syntax.json',
            ],
            'no-value' => [
                'no-value.json',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider noAudioProvider
     */
    public function noAudio(string $fixture)
    {
        /** @var HttpClient|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClient::class);
        $mocker = $httpClient->expects(
            $this->once()
        )->method('request');

        $this->expectResponse($mocker, $this->getFixture('failure/folder/' . $fixture));

        $this->assertEquals(new Feed([]), (new Client($httpClient, 'kremhrust', 'the-token'))->getFeed());
    }

    public static function noAudioProvider()
    {
        return [
            'no-attachments' => [
                'no-attachments.json',
            ],
            'no-audio' => [
                'no-audio.json',
            ],
        ];
    }

    private function createHttpClientMock(string $method, string $url, string $response)
    {
        $httpClient = $this->createMock(HttpClient::class);
        $mocker = $httpClient->expects(
            $this->once()
        )->method('request');

        $this->expectRequest($mocker, $method, $url);
        $this->expectResponse($mocker, $response);

        return $httpClient;
    }

    private function expectRequest($mocker, string $method, string $url)
    {
        return $mocker->with($method, $url);
    }

    private function expectResponse($mocker, string $response)
    {
        return $mocker->willReturn($this->createConfiguredMock(MessageInterface::class, [
            'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                '__toString' => $response,
            ])
        ]));
    }

    private function getFixture(string $file)
    {
        return file_get_contents(__DIR__ . '/Client/fixtures/' . $file);
    }
}
