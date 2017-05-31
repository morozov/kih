<?php

declare(strict_types=1);

namespace KiH\Client;

use GuzzleHttp\Client as HttpClient;
use KiH\Client;
use KiH\Entity\Folder;
use KiH\Entity\Media;
use KiH\Exception;
use Psr\Http\Message\StreamInterface;

final class OneDrive implements Client
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

    public function getFolder() : Folder
    {
        return Folder::fromApiResponse(
            $this->decode(
                $this->request(['root', 'children'], [
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
                ])
            )
        );
    }

    public function getMedia(string $id) : Media
    {
        return Media::fromApiResponse(
            $this->decode(
                $this->request(['items', $id])
            )
        );
    }

    private function request(array $path, array $query = []) : StreamInterface
    {
        $url = self::REST_API . '/' . implode('/', array_map('rawurlencode', array_merge([
            'shares',
            'u!' . base64_encode(
                self::SHARE_API . '?' . http_build_query($this->share)
            ),
        ], $path)));

        if (count($query) > 0) {
            $url .= '?' . http_build_query($query);
        }

        return $this->client
            ->request('GET', $url)
            ->getBody();
    }

    private function decode(StreamInterface $response) : array
    {
        $data = json_decode((string) $response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Cannot decode API response: ' . json_last_error_msg());
        }

        return $data;
    }
}
