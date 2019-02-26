<?php declare(strict_types=1);

namespace KiH\Providers\Vk;

use DateTime;
use GuzzleHttp\Client as HttpClient;
use KiH\Client as ClientInterface;
use KiH\Entity\Feed;
use KiH\Entity\Item;
use KiH\Entity\Media;
use KiH\Exception;
use Psr\Http\Message\StreamInterface;
use const JSON_ERROR_NONE;
use function array_filter;
use function array_map;
use function array_merge;
use function http_build_query;
use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use function rawurlencode;
use function sprintf;

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
        $this->client      = $client;
        $this->groupName   = $groupName;
        $this->accessToken = $accessToken;
    }

    public function getFeed() : Feed
    {
        return $this->createFeed(
            $this->decode(
                $this->call('wall.search', [
                    'domain' => $this->groupName,
                    'query' => 'Аудиозапись эфира',
                    'owners_only' => 1,
                    'count' => 10,
                ])
            )
        );
    }

    public function getMedia(string $id) : Media
    {
        return $this->createMedia(
            $this->decode(
                $this->call('audio.getById', [
                    'audios' => $id,
                ])
            )
        );
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
     * @return mixed[]
     *
     * @throws Exception
     */
    private function decode(StreamInterface $response) : array
    {
        $data = json_decode((string) $response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Cannot decode API response: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * @param mixed[] $data
     */
    private function createItem(array $data) : ?Item
    {
        if (! isset($data['attachments'])) {
            return null;
        }

        $audio = $this->findAttachment($data['attachments'], 'audio');

        if (! $audio) {
            return null;
        }

        $id = sprintf('%s_%s', $audio['owner_id'], $audio['id']);

        return new Item(
            $id,
            $audio['title'],
            new DateTime('@' . $data['date']),
            $id,
            $audio['duration'],
            'audio/mpeg',
            $data['text']
        );
    }

    /**
     * @param mixed[] $data
     *
     * @throws Exception
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
     * @return mixed[]|null
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
