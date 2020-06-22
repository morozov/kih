<?php

declare(strict_types=1);

namespace KiH\Entity;

use ArrayIterator;
use Iterator;
use IteratorAggregate;

/**
 * @psalm-immutable
 */
final class Feed implements IteratorAggregate
{
    /**
     * @var array<int,Item>
     */
    private array $files;

    /**
     * @param array<int,Item> $files
     */
    public function __construct(array $files)
    {
        $this->files = $files;
    }

    /**
     * @return ArrayIterator<int,Item>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->files);
    }
}
