<?php

declare(strict_types=1);

namespace KiH\Entity;

use DateTime;

final class File
{
    private $id;
    private $title;
    private $createdAt;
    private $url;
    private $duration;
    private $mimeType;

    public function __construct(
        string $id,
        string $title,
        DateTime $createdAt,
        string $url,
        int $duration,
        string $mimeType
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->createdAt = $createdAt;
        $this->url = $url;
        $this->duration = $duration;
        $this->mimeType = $mimeType;
    }

    public static function fromApiResponse(array $data): self
    {
        return new self(
            $data['id'],
            $data['audio']['title'],
            new DateTime($data['createdDateTime']),
            $data['webUrl'],
            $data['audio']['duration'],
            $data['file']['mimeType']
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }
}
