<?php

declare(strict_types=1);

namespace KiH\Tests;

use GuzzleHttp\Client as HttpClient;
use KiH\Client;
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
        $this->assertClientCallsApi([
            'foo' => 'bar',
        ], function (Client $client) {
            $client->getFolder();
        }, 'https://api.onedrive.com/v1.0/shares/u%21aHR0cHM6Ly9vbmVkcml2ZS5saXZlLmNvbS9yZWRpci5hc3B4P2Zvbz1iYXI%3D/root/children?select=audio%2CcreatedDateTime%2Cfile%2Cid%2Csize%2CwebUrl&orderby=lastModifiedDateTime+desc&top=10');
    }

    /**
     * @test
     */
    public function getItem()
    {
        $this->assertClientCallsApi([
            'foo' => 'bar',
        ], function (Client $client) {
            $client->getItem('baz');
        }, 'https://api.onedrive.com/v1.0/shares/u%21aHR0cHM6Ly9vbmVkcml2ZS5saXZlLmNvbS9yZWRpci5hc3B4P2Zvbz1iYXI%3D/items/baz');
    }

    private function assertClientCallsApi(array $share, callable $method, string $expected)
    {
        $httpClient = $this->createMock(HttpClient::class);
        $httpClient->expects(
            $this->once()
        )
            ->method('request')
            ->with('GET', $expected)
            ->willReturn($this->createConfiguredMock(MessageInterface::class, [
                'getBody' => $this->createMock(StreamInterface::class)
            ]));

        $client = new Client($httpClient, $share);
        $method($client);
    }
}
