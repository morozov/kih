<?php

declare(strict_types=1);

namespace KiH;

final class Generator
{
    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function generate(array $files) : string
    {
        $contents = '<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">
    <channel>
        <title>Кремов и Хрусталёв</title>
        <link>' . $this->encode($this->url('/')) . '</link>
        <itunes:image href="http://www.radiorecord.ru/i/img/rr-logo-podcast.png"/>
';

        foreach ($files as $file) {
            $contents .= '        <item>
            <title>' . $this->encode($file['audio']['title']) . '</title>
            <pubDate>' . $this->encode($file['createdDateTime']->format('r')). '</pubDate>
            <guid isPermaLink="false">' . $this->encode($file['webUrl']) . '</guid>
            <enclosure
                url="' . $this->encode($this->url('/media/' . rawurlencode($file['id']))) . '.mp3"
                length="' . $this->encode($file['audio']['duration']) . '"
                type="' . $this->encode($file['file']['mimeType']) . '"
            />
        </item>
';
        }

        $contents .= '    </channel>
</rss>
';

        return $contents;
    }

    private function encode($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
    }

    private function url($url)
    {
        return $this->namespace . $url;
    }
}
