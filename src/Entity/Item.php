<?php

declare(strict_types=1);

namespace KiH\Entity;

use DateTime;
use KiH\Exception;

final class Item
{
    /**
     * @var ?string
     */
    private $id;

    /**
     * @var ?string
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
     * @var ?int
     */
    private $duration;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var ?string
     */
    private $imageUrl;

    /**
     * @var ?string
     */
    private $description;

    public function __construct(
        ?string $id,
        ?string $url,
        string $title,
        DateTime $createdAt,
        string $guid,
        ?int $duration,
        string $mimeType,
        ?string $imageUrl,
        ?string $description
    ) {
        if (!($id === null xor $url === null)) {
            throw new Exception('One and only one of ID and URL should be specified');
        }

        $this->id = $id;
        $this->url = $url;
        $this->title = $title;
        $this->createdAt = $createdAt;
        $this->guid = $guid;
        $this->duration = $duration;
        $this->mimeType = $mimeType;
        $this->imageUrl = $imageUrl;
        $this->description = $description;
    }

    public static function fromApiResponse(array $data): self
    {
        return new self(
            $data['id'],
            null,
            $data['audio']['title'],
            new DateTime($data['createdDateTime']),
            $data['webUrl'],
            $data['audio']['duration'],
            $data['file']['mimeType'],
            null,
            null
        );
    }

    public function getId() : ?string
    {
        return $this->id;
    }

    public function getUrl() : ?string
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

    public function getDuration() : ?int
    {
        return $this->duration;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getImageUrl() : ?string
    {
        return $this->imageUrl;
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }
}
