<?php

declare(strict_types=1);

namespace KiH\Generator;

use DOMDocument;
use DOMElement;
use KiH\Entity\Feed;
use KiH\Entity\Item;
use KiH\Generator;
use Psr\Http\Message\UriInterface;
use Slim\Interfaces\RouteParserInterface;

final class Rss implements Generator
{
    private RouteParserInterface $routeParser;

    /**
     * @var string[]
     */
    private array $settings;

    /**
     * @param string[] $settings
     */
    public function __construct(RouteParserInterface $routeParser, array $settings)
    {
        $this->routeParser = $routeParser;
        $this->settings    = $settings;
    }

    public function generate(Feed $feed, UriInterface $requestUri): DOMDocument
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $rss      = $document->createElement('rss');
        $rss->setAttribute('version', '2.0');
        $document->appendChild($rss);

        $document->createAttributeNS(
            'http://www.itunes.com/dtds/podcast-1.0.dtd',
            'itunes:attr'
        );

        $channel = $document->createElement('channel');
        $rss->appendChild($channel);

        $title = $document->createElement('title');
        $title->appendChild(
            $document->createTextNode($this->settings['title'])
        );
        $channel->appendChild($title);

        $link = $document->createElement('link');
        $link->appendChild(
            $document->createTextNode(
                $this->routeParser->fullUrlFor($requestUri, 'index')
            )
        );
        $channel->appendChild($link);

        $image = $document->createElement('itunes:image');
        $image->setAttribute('href', $this->settings['logo']);
        $channel->appendChild($image);

        foreach ($feed as $file) {
            $channel->appendChild(
                $this->generateItem($document, $file, $requestUri)
            );
        }

        return $document;
    }

    private function generateItem(DOMDocument $document, Item $item, UriInterface $requestUri): DOMElement
    {
        $element = $document->createElement('item');

        $title = $document->createElement('title');
        $title->appendChild(
            $document->createTextNode($item->title)
        );
        $element->appendChild($title);

        $pubDate = $document->createElement('pubDate');
        $pubDate->appendChild(
            $document->createTextNode($item->createdAt->format('r'))
        );
        $element->appendChild($pubDate);

        $guid = $document->createElement('guid');
        $guid->setAttribute('isPermaLink', 'false');
        $guid->appendChild(
            $document->createTextNode($item->guid)
        );
        $element->appendChild($guid);

        $url = $this->routeParser->fullUrlFor($requestUri, 'media', [
            'id' => $item->id,
        ]);

        $enclosure = $document->createElement('enclosure');
        $enclosure->setAttribute('url', $url);

        $enclosure->setAttribute('length', (string) $item->duration);

        $enclosure->setAttribute('type', $item->mimeType);
        $element->appendChild($enclosure);

        $description = $document->createElement('description');
        $description->appendChild(
            $document->createTextNode($item->description)
        );
        $element->appendChild($description);

        return $element;
    }
}
