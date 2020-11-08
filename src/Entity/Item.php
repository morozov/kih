<?php

declare(strict_types=1);

namespace KiH\Entity;

use DateTimeImmutable;

/**
 * @psalm-immutable
 */
final class Item
{
    public string $id;

    public string $title;

    public DateTimeImmutable $createdAt;

    public string $guid;

    public int $duration;

    public string $mimeType;

    public string $description;

    public ?string $photo;

    public function __construct(
        string $id,
        string $title,
        DateTimeImmutable $createdAt,
        string $guid,
        int $duration,
        string $mimeType,
        string $description,
        ?string $photo
    ) {
        $this->id          = $id;
        $this->title       = $title;
        $this->createdAt   = $createdAt;
        $this->guid        = $guid;
        $this->duration    = $duration;
        $this->mimeType    = $mimeType;
        $this->description = $description;
        $this->photo       = $photo;
    }
}
