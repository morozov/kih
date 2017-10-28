<?php

declare(strict_types=1);

namespace KiH\Entity;

use DateTime;

final class Item
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $title;

    /**
     * @var DateTime
     */
    private $createdAt;

    /**
     * @var string
     */
    private $guid;

    /**
     * @var int
     */
    private $duration;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var string
     */
    private $description;

    public function __construct(
        string $url,
        string $title,
        DateTime $createdAt,
        string $guid,
        int $duration,
        string $mimeType,
        string $description
    ) {
        $this->url = $url;
        $this->title = $title;
        $this->createdAt = $createdAt;
        $this->guid = $guid;
        $this->duration = $duration;
        $this->mimeType = $mimeType;
        $this->description = $description;
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getGuid(): string
    {
        return $this->guid;
    }

    public function getDuration() : int
    {
        return $this->duration;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getDescription() : string
    {
        return $this->description;
    }
}