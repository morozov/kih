<?php declare(strict_types=1);

namespace KiH\Tests\Providers\Vk;

use DateTimeImmutable;
use GuzzleHttp\Client as HttpClient;
use KiH\Entity\Feed;
use KiH\Entity\Item;
use KiH\Entity\Media;
use KiH\Exception;
use KiH\Providers\Vk\Client;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use function assert;
use function file_get_contents;
use function is_string;

class ClientTest extends TestCase
{
    /**
     * @test
     */
    public function getFeed() : void
    {
// @codingStandardsIgnoreStart
        $httpClient = $this->createHttpClientMock(
            'GET',
            'https://api.vk.com/method/wall.search?domain=kremhrust&query=%D0%AD%D1%84%D0%B8%D1%80&owners_only=1&count=10&access_token=the-token&v=5.68',
            $this->getFixture('success/feed.json')
        );
// @codingStandardsIgnoreEnd

        $this->assertEquals(new Feed([
            new Item(
                '2000209538_456241259',
                'Эфир от 21 сентября 2017',
                new DateTimeImmutable('2017-09-21T19:14:00.000000+0000'),
                '2000209538_456241259',
                3500,
                'audio/mpeg',
                <<<EOF
Аудиозапись эфира от 21 сентября 2017 (четверг) 
 
Архив аудиозаписей с возможностью загрузки: https://vk.cc/6X3JkI
EOF
            ),
        ]), $this->getClient($httpClient)->getFeed());
    }

    /**
     * @test
     * @dataProvider feedFailureProvider
     */
    public function feedParseFailure(string $fixture) : void
    {
        /** @var HttpClient|MockObject $httpClient */
        $httpClient = $this->createHttpClientMockFromFixture('failure/folder/' . $fixture);

        $this->expectException(Exception::class);
        $this->getClient($httpClient)->getFeed();
    }

    /**
     * @return mixed[][]
     */
    public static function feedFailureProvider() : iterable
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
    public function noAudio(string $fixture) : void
    {
        /** @var HttpClient|MockObject $httpClient */
        $httpClient = $this->createHttpClientMockFromFixture('failure/folder/' . $fixture);

        $this->assertEquals(new Feed([]), $this->getClient($httpClient)->getFeed());
    }

    /**
     * @return mixed[][]
     */
    public static function noAudioProvider() : iterable
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

    /**
     * @test
     */
    public function getMedia() : void
    {
// @codingStandardsIgnoreStart
        $httpClient = $this->createHttpClientMock(
            'GET',
            'https://api.vk.com/method/audio.getById?audios=2000003614_456241126&access_token=the-token&v=5.68',
            $this->getFixture('success/media.json')
        );

        $this->assertEquals(
            new Media('https://cs9-7v4.vkuseraudio.net/p17/a8b2cfb8ae3359.mp3?extra=W4PQNJGOG68l6GIUdT4wioaCBzBSGaYVYHxivvLuhmGC77JP5N53SdDDL7XCXp7OWyoD3uBwKNKSH8Hh68xJD-TYl9VHJPejM6v20uS1zFlz1-T-6h6k3dbfgznbyVcsTm6qV9Pibwr_Cw'),
            $this->getClient($httpClient)->getMedia('2000003614_456241126')
        );
// @codingStandardsIgnoreEnd
    }

    /**
     * @test
     * @dataProvider mediaFailureProvider
     */
    public function mediaParseFailure(string $fixture) : void
    {
        /** @var HttpClient|MockObject $httpClient */
        $httpClient = $this->createHttpClientMockFromFixture('failure/media/' . $fixture);

        $this->expectException(Exception::class);
        $this->getClient($httpClient)->getMedia('2000003614_456241126');
    }

    /**
     * @return mixed[][]
     */
    public static function mediaFailureProvider() : iterable
    {
        return [
            'no-value' => [
                'no-value.json',
            ],
        ];
    }

    private function getClient(HttpClient $httpClient) : Client
    {
        return new Client($httpClient, 'kremhrust', 'the-token');
    }

    private function createHttpClientMock(string $method, string $url, string $response) : HttpClient
    {
        $httpClient = $this->createMock(HttpClient::class);
        $mocker     = $httpClient->expects(
            $this->once()
        )->method('request');

        $this->expectRequest($mocker, $method, $url);
        $this->expectResponse($mocker, $response);

        return $httpClient;
    }

    private function createHttpClientMockFromFixture(string $fixture) : HttpClient
    {
        $httpClient = $this->createMock(HttpClient::class);
        $mocker     = $httpClient->expects(
            $this->once()
        )->method('request');

        $this->expectResponse($mocker, $this->getFixture($fixture));

        return $httpClient;
    }

    private function expectRequest(InvocationMocker $mocker, string $method, string $url) : InvocationMocker
    {
        return $mocker->with($method, $url);
    }

    private function expectResponse(InvocationMocker $mocker, string $response) : InvocationMocker
    {
        return $mocker->willReturn($this->createConfiguredMock(MessageInterface::class, [
            'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                '__toString' => $response,
            ]),
        ]));
    }

    private function getFixture(string $file) : string
    {
        $fixture = file_get_contents(__DIR__ . '/Client/fixtures/' . $file);
        assert(is_string($fixture));

        return $fixture;
    }
}
