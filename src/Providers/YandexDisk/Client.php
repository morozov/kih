<?php

declare(strict_types=1);

namespace KiH\Providers\YandexDisk;

use DateTime;
use GuzzleHttp\Client as HttpClient;
use IntlDateFormatter;
use KiH\Client as ClientInterface;
use KiH\Entity\Item;
use KiH\Entity\Feed;
use KiH\Entity\Media;
use KiH\Exception;
use Psr\Http\Message\StreamInterface;

final class Client implements ClientInterface
{
    private const API = 'https://cloud-api.yandex.net/v1/disk';

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var IntlDateFormatter
     */
    private $formatter;

    /**
     * @var string
     */
    private $publicKey;

    public function __construct(HttpClient $client, string $publicKey)
    {
        $this->client = $client;
        $this->publicKey = $publicKey;

        $this->formatter = new IntlDateFormatter(
            'ru_RU',
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE
        );
    }

    public function getFeed() : Feed
    {
        return $this->createFeed(
            $this->decode(
                $this->request(['public', 'resources'], [
                    'path' => $this->getFolderPath(new DateTime()),
                    'sort' => '-name',
                    'limit' => 10,
                ])
            )
        );
    }

    public function getMedia(string $id) : Media
    {
        return $this->createMedia(
            $this->decode(
                $this->request(['public', 'resources', 'download'], [
                    'path' => $this->getPathById($id),
                ])
            )
        );
    }

    private function getFolderPath(DateTime $date) : string
    {
        $this->formatter->setPattern('/yyyy год/MM LLLL');

        // capitalize the first letter of the month name
        return preg_replace_callback('/(.*?)(\S)(\S+)$/u', function (array $matches) : string {
            return $matches[1] . mb_strtoupper($matches[2]) . $matches[3];
        }, $this->formatter->format($date));
    }

    private function request(array $path, array $query = []) : StreamInterface
    {
        $url = self::API . '/' . implode('/', array_map('rawurlencode', $path))
            . '?' . http_build_query(array_merge([
                'public_key' => $this->publicKey,
            ], $query));

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

    private function createItem(array $data) : Item
    {
        return new Item(
            $this->getIdByFileName($data['name']),
            null,
            pathinfo($data['name'], PATHINFO_FILENAME),
            new DateTime($data['created']),
            $data['resource_id'],
            null,
            $data['mime_type'],
            null,
            null
        );
    }

    private function createFeed(array $data) : Feed
    {
        if (!isset($data['_embedded']['items'])) {
            throw new Exception('The folder representation does not contain the "_embedded.items" element');
        }

        return new Feed(array_map(function (array $file) : Item {
            return $this->createItem($file);
        }, $data['_embedded']['items']));
    }

    private function createMedia(array $data) : Media
    {
        return new Media($data['href']);
    }

    private function getIdByFileName(string $name) : string
    {
        if (preg_match('/^Эфир от (\d{2})\.(\d{2})\.(\d{4})\.mp3$/', $name, $match)) {
            $id = sprintf('%s%s%s', $match[3], $match[2], $match[1]);
        } else {
            $id = 'FIXME';
        }

        return $id;
    }

    private function getPathById(string $id) : string
    {
        $date = new DateTime($id);

        return $this->getFolderPath($date)
            . sprintf('/Эфир от %s.mp3', $date->format('d.m.Y'));
    }
}
