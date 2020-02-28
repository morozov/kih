<?php declare(strict_types=1);

namespace KiH\Entity;

/**
 * @psalm-immutable
 */
final class Media
{
    public string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }
}
