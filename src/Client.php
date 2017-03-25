<?php

declare(strict_types=1);

namespace KiH;

use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\StreamInterface;

final class Client
{
    private const API = 'https://api.onedrive.com/v1.0';

    /** @var HttpClient */
    private $client;

    /** @var string */
    private $share;

    public function __construct(HttpClient $client, string $share)
    {
        $this->client = $client;
        $this->share = $share;
    }

    public function getFolder() : StreamInterface
    {
        $url = self::API . '/shares/u!' . base64_encode($this->share) . '/root/children?'
            . http_build_query([
                'select' => implode(',', [
                    'audio',
                    'createdDateTime',
                    'file',
                    'id',
                    'size',
                    'webUrl',
                ]),
                'orderby' => 'lastModifiedDateTime desc',
                'top' => 10,
            ]);

        return $this->client
            ->request('GET', $url)
            ->getBody();
    }

    public function getItem(string $id) : StreamInterface
    {
        $url = self::API . '/shares/u!' . base64_encode($this->share) . '/items/' . rawurlencode($id);

        return $this->client
            ->request('GET', $url)
            ->getBody();
    }
}
