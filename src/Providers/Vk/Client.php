<?php declare(strict_types=1);

namespace KiH\Providers\Vk;

use DateTimeImmutable;
use GuzzleHttp\Client as HttpClient;
use KiH\Client as ClientInterface;
use KiH\Entity\Feed;
use KiH\Entity\Item;
use KiH\Entity\Media;
use KiH\Exception;
use Psr\Http\Message\StreamInterface;
use function array_filter;
use function array_map;
use function array_merge;
use function http_build_query;
use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use function rawurlencode;
use function sprintf;
use const JSON_ERROR_NONE;

/**
 * @psalm-type Audio = array{id: int, owner_id: int, title: string, duration: int, url: string}
 * @psalm-type Attachment = array{type: string, audio: Audio}
 * @psalm-type ItemStruct = array{date: string, text:string, attachments: list<Attachment>}
 * @psalm-type FeedData = array{response: array{items: list<ItemStruct>}}
 * @psalm-type MediaData = array{response: list<Audio>}
 */
final class Client implements ClientInterface
{
    private const API_URL = 'https://api.vk.com';

    private const API_VERSION = '5.68';

    private HttpClient $client;

    private string $groupName;

    private string $accessToken;

    public function __construct(HttpClient $client, string $groupName, string $accessToken)
    {
        $this->client      = $client;
        $this->groupName   = $groupName;
        $this->accessToken = $accessToken;
    }

    public function getFeed() : Feed
    {
        /** @psalm-var FeedData $data */
        $data = $this->decode(
            $this->call('wall.search', [
                'domain' => $this->groupName,
                'query' => 'Эфир',
                'owners_only' => 1,
                'count' => 10,
            ])
        );

        return $this->createFeed($data);
    }

    public function getMedia(string $id) : Media
    {
        /** @psalm-var MediaData $data */
        $data = $this->decode(
            $this->call('audio.getById', [
                'audios' => $id,
            ])
        );

        return $this->createMedia($data);
    }

    /**
     * @param mixed[] $params Query parameters
     */
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

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    private function decode(StreamInterface $response) : array
    {
        /** @var array<string, mixed> $data */
        $data = json_decode((string) $response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Cannot decode API response: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * @param mixed[] $item
     *
     * @psalm-param ItemStruct $item
     */
    private function createItem(array $item) : ?Item
    {
        if (! isset($item['attachments'])) {
            return null;
        }

        $audio = $this->findAttachment($item['attachments'], 'audio');

        if (! $audio) {
            return null;
        }

        $id = sprintf('%d_%d', $audio['owner_id'], $audio['id']);

        return new Item(
            $id,
            $audio['title'],
            new DateTimeImmutable('@' . $item['date']),
            $id,
            $audio['duration'],
            'audio/mpeg',
            $item['text']
        );
    }

    /**
     * @param mixed[] $data
     *
     * @throws Exception
     *
     * @psalm-param FeedData $data
     */
    private function createFeed(array $data) : Feed
    {
        if (! isset($data['response']['items'])) {
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

    /**
     * @param mixed[] $data
     *
     * @throws Exception
     *
     * @psalm-param MediaData $data
     */
    private function createMedia(array $data) : Media
    {
        if (! isset($data['response'][0]['url'])) {
            throw new Exception('The response does not contain the "response.0.url" element');
        }

        return new Media($data['response'][0]['url']);
    }

    /**
     * @param mixed[] $data
     *
     * @return array<string,mixed>|null
     *
     * @psalm-param list<Attachment> $data
     * @psalm-param 'audio' $type
     * @psalm-return Audio|null
     */
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
