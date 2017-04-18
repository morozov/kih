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
    private const SHARE_API = 'https://onedrive.live.com/redir.aspx';

    private const REST_API = 'https://api.onedrive.com/v1.0';

    /** @var HttpClient */
    private $client;

    /** @var array */
    private $share;

    public function __construct(HttpClient $client, array $share)
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
        $url = self::REST_API . '/' . implode('/', array_map('rawurlencode', array_merge([
            'shares',
            'u!' . base64_encode(
                self::SHARE_API . '?' . http_build_query($this->share)
            ),
        ], $path)));

        if ($query) {
            $url .= '?' . http_build_query($query);
        }

        return $this->client
            ->request('GET', $url)
            ->getBody();
    }
}
