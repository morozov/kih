<?php

declare(strict_types=1);

namespace KiH\Providers\Vk;

use DateTime;
use GuzzleHttp\Client as HttpClient;
use KiH\Client as ClientInterface;
use KiH\Entity\Item;
use KiH\Entity\Feed;
use KiH\Exception;
use Psr\Http\Message\StreamInterface;

final class Client implements ClientInterface
{
    private const API_URL = 'https://api.vk.com';

    private const API_VERSION = '5.68';

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var string
     */
    private $groupName;

    /**
     * @var string
     */
    private $accessToken;

    public function __construct(HttpClient $client, string $groupName, string $accessToken)
    {
        $this->client = $client;
        $this->groupName = $groupName;
        $this->accessToken = $accessToken;
    }

    public function getFeed() : Feed
    {
        return $this->createFeed(
            $this->decode(
                $this->call('wall.search', [
                    'domain' => $this->groupName,
                    'query' => 'Аудиозапись эфира',
                    'owners_only' => true,
                    'count' => 10,
                ])
            )
        );
    }

    private function call(string $method, array $params) : StreamInterface
    {
        $url = sprintf(
            '%s/method/%s?%s',
            self::API_URL,
            rawurlencode($method),
            http_build_query(
                array_merge($params, [
                    'access_token' => $this->accessToken,
                    'v' => self::API_VERSION,
                ])
            )
        );

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

    private function createItem(array $data) : ?Item
    {
        if (!isset($data['attachments'])) {
            return null;
        }

        $audio = $this->findAttachment($data['attachments'], 'audio');

        if (!$audio) {
            return null;
        }

        return new Item(
            $audio['url'],
            $audio['title'],
            new DateTime('@' . $data['date']),
            (string) $data['id'],
            $audio['duration'],
            'audio/mpeg',
            $data['text']
        );
    }

    private function createFeed(array $data) : Feed
    {
        if (!isset($data['response']['items'])) {
            throw new Exception('The response does not contain the "response.items" element');
        }

        return new Feed(
            array_filter(
                array_map(function (array $file) : ?Item {
                    return $this->createItem($file);
                }, $data['response']['items'])
            )
        );
    }

    private function findAttachment(array $data, string $type) : ?array
    {
        foreach ($data as $attachment) {
            if ($attachment['type'] === $type) {
                return $attachment[$type];
            }
        }

        return null;
    }
}
