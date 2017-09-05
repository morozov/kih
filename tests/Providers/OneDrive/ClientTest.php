<?php

declare(strict_types=1);

namespace KiH\Tests\Providers\OneDrive;

use GuzzleHttp\Client as HttpClient;
use KiH\Providers\OneDrive\Client;
use KiH\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class ClientTest extends TestCase
{
    /**
     * @test
     */
    public function getFolder()
    {
        $httpClient = $this->createHttpClientMock(
            'GET',
// @codingStandardsIgnoreStart
            'https://api.onedrive.com/v1.0/shares/u%21aHR0cHM6Ly9vbmVkcml2ZS5saXZlLmNvbS9yZWRpci5hc3B4P2Zvbz1iYXI%3D/root/children?select=audio%2CcreatedDateTime%2Cfile%2Cid%2Csize%2CwebUrl&orderby=lastModifiedDateTime+desc&top=10',
// @codingStandardsIgnoreEnd
            $this->getFixture('success/folder.json')
        );

        (new Client($httpClient, [
            'foo' => 'bar',
        ]))->getFolder();
    }

    /**
     * @test
     */
    public function getItem()
    {
        $httpClient = $this->createHttpClientMock(
            'GET',
// @codingStandardsIgnoreStart
            'https://api.onedrive.com/v1.0/shares/u%21aHR0cHM6Ly9vbmVkcml2ZS5saXZlLmNvbS9yZWRpci5hc3B4P2Zvbz1iYXI%3D/items/baz',
// @codingStandardsIgnoreEnd
            $this->getFixture('success/item.json')
        );

        (new Client($httpClient, [
            'foo' => 'bar',
        ]))->getMedia('baz');
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
        (new Client($httpClient, []))->getFolder();
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
