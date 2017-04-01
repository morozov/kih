<?php

declare(strict_types=1);

namespace KiH;

use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\StreamInterface;
use function array_map;
use function array_merge;
use function http_build_query;
use function implode;

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
        return $this->request(['root', 'children'], [
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
    }

    public function getItem(string $id) : StreamInterface
    {
        return $this->request(['items', $id]);
    }

    private function request(array $path, array $query = []) : StreamInterface
    {
        $url = self::API . '/' . implode('/', array_map('rawurlencode', array_merge([
            'shares',
            'u!' . base64_encode($this->share),
        ], $path)));

        if ($query) {
            $url .= '?' . http_build_query($query);
        }

        return $this->client
            ->request('GET', $url)
            ->getBody();
    }
}
