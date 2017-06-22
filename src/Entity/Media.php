<?php

declare(strict_types=1);

namespace KiH\Entity;

final class Media
{
    /**
     * @var string
     */
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public static function fromApiResponse(array $data) : self
    {
        return new self($data['@content.downloadUrl']);
    }

    public function getUrl() : string
    {
        return $this->url;
    }
}
