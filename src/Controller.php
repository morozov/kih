<?php

declare(strict_types=1);

namespace KiH;

final class Controller
{
    /** @var Client */
    private $client;

    /** @var Parser */
    private $parser;

    /** @var Parser */
    private $generator;

    public function __construct(Client $client, Parser $parser, Generator $generator)
    {
        $this->client = $client;
        $this->parser = $parser;
        $this->generator = $generator;
    }

    public function rss()
    {
        $stream = $this->client->getFolder();
        $files = $this->parser->parseFolder((string) $stream);
        $rss = $this->generator->generate($files);

        header('Content-Type: text/xml; charset=UTF-8');
        echo $rss;
    }

    public function download(string $id)
    {
        $stream = $this->client->getItem($id);
        $item = $this->parser->parseItem((string) $stream);

        header('Location: ' . $item['@content.downloadUrl']);
    }
}
