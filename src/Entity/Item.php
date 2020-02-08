<?php declare(strict_types=1);

namespace KiH\Entity;

use DateTimeImmutable;

final class Item
{
    private string $id;

    private string $title;

    private DateTimeImmutable $createdAt;

    private string $guid;

    private int $duration;

    private string $mimeType;

    private string $description;

    public function __construct(
        string $id,
        string $title,
        DateTimeImmutable $createdAt,
        string $guid,
        int $duration,
        string $mimeType,
        string $description
    ) {
        $this->id          = $id;
        $this->title       = $title;
        $this->createdAt   = $createdAt;
        $this->guid        = $guid;
        $this->duration    = $duration;
        $this->mimeType    = $mimeType;
        $this->description = $description;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function getCreatedAt() : DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getGuid() : string
    {
        return $this->guid;
    }

    public function getDuration() : int
    {
        return $this->duration;
    }

    public function getMimeType() : string
    {
        return $this->mimeType;
    }

    public function getDescription() : string
    {
        return $this->description;
    }
}
