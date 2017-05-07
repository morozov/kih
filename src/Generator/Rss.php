<?php

declare(strict_types=1);

namespace KiH\Generator;

use DOMDocument;
use DOMElement;
use KiH\Entity\File;
use KiH\Entity\Folder;
use KiH\Generator;
use Slim\Interfaces\RouterInterface;

final class Rss implements Generator
{
    /** @var RouterInterface $router */
    private $router;
    private $settings;

    public function __construct(RouterInterface $router, array $settings)
    {
        $this->router = $router;
        $this->settings = $settings;
    }

    public function generate(Folder $folder) : DOMDocument
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

        foreach ($folder as $file) {
            $channel->appendChild(
                $this->generateItem($document, $file)
            );
        }

        return $document;
    }

    private function generateItem(DOMDocument $document, File $file) : DOMElement
    {
        $item = $document->createElement('item');

        $title = $document->createElement('title');
        $title->appendChild(
            $document->createTextNode($file->getTitle())
        );
        $item->appendChild($title);

        $pubDate = $document->createElement('pubDate');
        $pubDate->appendChild(
            $document->createTextNode($file->getCreatedAt()->format('r'))
        );
        $item->appendChild($pubDate);

        $guid = $document->createElement('guid');
        $guid->setAttribute('isPermaLink', 'false');
        $guid->appendChild(
            $document->createTextNode($file->getUrl())
        );
        $item->appendChild($guid);

        $enclosure = $document->createElement('enclosure');
        $enclosure->setAttribute(
            'url',
            $this->router->pathFor('media', ['id' => $file->getId()])
        );
        $enclosure->setAttribute('length', (string) $file->getDuration());
        $enclosure->setAttribute('type', $file->getMimeType());
        $item->appendChild($enclosure);

        return $item;
    }
}
