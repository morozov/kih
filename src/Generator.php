<?php

declare(strict_types=1);

namespace KiH;

use DOMDocument;
use DOMElement;
use Slim\Interfaces\RouterInterface;

final class Generator
{
    /** @var RouterInterface $router */
    private $router;
    private $settings;

    public function __construct(RouterInterface $router, array $settings)
    {
        $this->router = $router;
        $this->settings = $settings;
    }

    public function generate(array $files) : DOMDocument
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $rss = $document->createElement('rss');
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
                $this->router->pathFor('index')
            )
        );
        $channel->appendChild($link);

        $image = $document->createElement('itunes:image');
        $image->setAttribute('href', $this->settings['logo']);
        $channel->appendChild($image);

        foreach ($files as $file) {
            $channel->appendChild(
                $this->generateItem($document, $file)
            );
        }

        return $document;
    }

    private function generateItem(DOMDocument $document, array $file) : DOMElement
    {
        $item = $document->createElement('item');

        $title = $document->createElement('title');
        $title->appendChild(
            $document->createTextNode($file['audio']['title'])
        );
        $item->appendChild($title);

        $pubDate = $document->createElement('pubDate');
        $pubDate->appendChild(
            $document->createTextNode($file['createdDateTime']->format('r'))
        );
        $item->appendChild($pubDate);

        $guid = $document->createElement('guid');
        $guid->setAttribute('isPermaLink', 'false');
        $guid->appendChild(
            $document->createTextNode($file['webUrl'])
        );
        $item->appendChild($guid);

        $enclosure = $document->createElement('enclosure');
        $enclosure->setAttribute(
            'url',
            $this->router->pathFor('media', $file)
        );
        $enclosure->setAttribute('length', (string) $file['audio']['duration']);
        $enclosure->setAttribute('type', $file['file']['mimeType']);
        $item->appendChild($enclosure);

        return $item;
    }
}
