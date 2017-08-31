<?php

declare(strict_types=1);

namespace KiH\Entity;

use DateTime;

final class File
{
    /**
     * @var string
     */
    private $id;

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
     * @var ?int
     */
    private $duration;

    /**
     * @var string
     */
    private $mimeType;

    public function __construct(
        string $id,
        string $title,
        DateTime $createdAt,
        string $guid,
        ?int $duration,
        string $mimeType
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->createdAt = $createdAt;
        $this->guid = $guid;
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

    public function getGuid(): string
    {
        return $this->guid;
    }

    public function getDuration() : ?int
    {
        return $this->duration;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }
}
